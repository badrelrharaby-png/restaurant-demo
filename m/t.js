/* Opvolging van de mail aan zaakvoerders.
 * Wie via een link in de mail komt, heeft ?m=<code> in de url. We bewaren die code in deze browser,
 * zodat ook latere stappen (video bekijken, aanmelden, afspraak boeken) bij dat restaurant terechtkomen.
 * Gebruik: <script src="/m/t.js" data-page="video"></script>  en  window.ffMail.track('geboekt')
 */
(function () {
  var API = 'https://vbxbtkkbeutmaqcfcotn.supabase.co/rest/v1/rpc/';
  var KEY = 'sb_publishable_UiRbAIliOFBxLkNiWf7qVw_a2ciV8d3';
  var OK = /^[a-f0-9]{32}$/;
  var qs = new URLSearchParams(location.search);
  var t = qs.get('m');
  try {
    if (t && OK.test(t)) localStorage.setItem('ff_m', t);
    else t = localStorage.getItem('ff_m');
  } catch (e) { /* privévenster: alleen deze pagina */ }
  if (!t || !OK.test(t)) t = null;
  // De code niet in de adresbalk laten staan (zodat ze niet mee gedeeld wordt).
  if (qs.has('m') && history.replaceState) {
    qs.delete('m');
    var rest = qs.toString();
    history.replaceState(history.state, '', location.pathname + (rest ? '?' + rest : '') + location.hash);
  }
  function call(name, params) {
    return fetch(API + name, { method: 'POST', keepalive: true, headers: { apikey: KEY, 'Content-Type': 'application/json' }, body: JSON.stringify(params) })
      .then(function (r) { return r.ok ? r.json() : null; }).catch(function () { return null; });
  }
  window.ffMail = {
    token: t,
    track: function (kind) { return t ? call('lead_track', { p_token: t, p_kind: kind }) : Promise.resolve(null); },
    info: function () { return t ? call('lead_mail_info', { p_token: t }) : Promise.resolve(null); }
  };
  var el = document.currentScript;
  var page = el && el.getAttribute('data-page');
  if (page) window.ffMail.track(page);
})();
