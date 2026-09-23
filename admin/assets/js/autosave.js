/* ===== Autoguardado de borradores (panel admin) =====
 * El formulario declara data-autosave="1", data-tabla, data-id, data-pk.
 * Guarda en localStorage cada vez que se edita y, al cerrar la pagina a
 * mitad de camino, el borrador queda disponible para restaurarlo despues.
 */
(function () {
  var DATE = Date.now;

  function getForm() {
    return document.querySelector('form[data-autosave="1"]');
  }

  function getKey(form) {
    return 'ddg_borrador_' + form.getAttribute('data-tabla') + '_' + form.getAttribute('data-id');
  }

  function collect(form) {
    var data = { ts: DATE() };

    // CKEditor vuelca su contenido al textarea oculto antes de leer los valores.
    if (window.CKEDITOR && CKEDITOR.instances) {
      for (var k in CKEDITOR.instances) {
        try { CKEDITOR.instances[k].updateElement(); } catch (e) {}
      }
    }

    // El editor WYSIWYG vuelca su contenido al textarea oculto.
    var editors = form.querySelectorAll('.wysiwyg-editor');
    for (var i = 0; i < editors.length; i++) {
      var obj = editors[i].getAttribute('data-objetivo');
      var hidden = obj ? document.getElementById('wysiwyg_hidden_' + obj) : null;
      if (hidden) hidden.value = editors[i].innerHTML;
    }

    var els = form.querySelectorAll('input, select, textarea');
    for (var j = 0; j < els.length; j++) {
      var el = els[j];
      if (el.type === 'password' || el.type === 'file') continue;
      if (el.type === 'checkbox') {
        data[el.name] = el.checked ? '1' : '0';
      } else if (el.type === 'radio') {
        if (el.checked) data[el.name] = el.value;
      } else {
        data[el.name] = el.value;
      }
    }
    return data;
  }

  function restore(form, data) {
    if (!data) return;

    var els = form.querySelectorAll('input, select, textarea');
    for (var i = 0; i < els.length; i++) {
      var el = els[i];
      if (el.type === 'password' || el.type === 'file') continue;
      if (!(el.name in data)) continue;
      if (el.type === 'checkbox') {
        el.checked = data[el.name] === '1';
      } else {
        el.value = data[el.name];
      }
    }

    // Rellenar tambien el editor CKEditor (si ya cargo) y el editor WYSIWYG.
    var ckId = 'wysiwyg_hidden_desarrollo';
    var ckTa = document.getElementById(ckId);
    if (ckTa && window.CKEDITOR && CKEDITOR.instances[ckId]) {
      var ck = CKEDITOR.instances[ckId];
      var aplicar = function () { ck.setData(ckTa.value || ''); };
      if (ck.status === 'ready') aplicar();
      else ck.once('instanceReady', aplicar);
    }
    var editors = form.querySelectorAll('.wysiwyg-editor');
    for (var j = 0; j < editors.length; j++) {
      var obj = editors[j].getAttribute('data-objetivo');
      var hidden = obj ? document.getElementById('wysiwyg_hidden_' + obj) : null;
      if (hidden) editors[j].innerHTML = hidden.value || '';
    }
  }

  function hasRelevantData(data) {
    if (!data) return false;
    var count = 0;
    for (var k in data) {
      if (k === 'ts') continue;
      if (data[k] && String(data[k]).trim() !== '' && String(data[k]) !== '0') count++;
    }
    return count > 0;
  }

  function horaRegistro(ts) {
    var d = new Date(ts);
    var hh = ('0' + d.getHours()).slice(-2);
    var mm = ('0' + d.getMinutes()).slice(-2);
    var ss = ('0' + d.getSeconds()).slice(-2);
    return hh + ':' + mm + ':' + ss;
  }

  function setUp() {
    var form = getForm();
    if (!form) return;

    var key = getKey(form);
    var status = document.getElementById('autosave_status');
    var banner = document.getElementById('autosave_banner');
    var timer = null;

    function saveNow() {
      var data = collect(form);
      if (!hasRelevantData(data)) return;
      try {
        localStorage.setItem(key, JSON.stringify(data));
      } catch (e) { /* almacenamiento no disponible */ }
      if (status) status.textContent = 'Borrador autoguardado ' + horaRegistro(data.ts);
    }

    function scheduleSave() {
      if (timer) clearTimeout(timer);
      timer = setTimeout(saveNow, 1200);
    }

    form.addEventListener('input', scheduleSave);
    form.addEventListener('change', scheduleSave);

    window.addEventListener('pagehide', function () {
      if (!form.hasAttribute('data-sent')) saveNow();
    });
    form.addEventListener('submit', function () {
      form.setAttribute('data-sent', '1');
      try { localStorage.removeItem(key); } catch (e) {}
    });

    // Al abrir el formulario: ofrecer restaurar el borrador pendiente.
    var stored = null;
    try { stored = JSON.parse(localStorage.getItem(key) || 'null'); } catch (e) {}

    if (stored && hasRelevantData(stored) && banner) {
      banner.classList.remove('hidden');
      var info = banner.querySelector('p');
      if (info) info.innerHTML = info.innerHTML + ' <span class="text-theme-xs font-normal">(guardado a las ' + horaRegistro(stored.ts) + ')</span>';

      var restaurar = document.getElementById('autosave_restaurar');
      var descartar = document.getElementById('autosave_descartar');
      if (restaurar) {
        restaurar.addEventListener('click', function () {
          restore(form, stored);
          banner.classList.add('hidden');
          if (status) status.textContent = 'Borrador restaurado. Recuerda guardar cuando termines.';
        });
      }
      if (descartar) {
        descartar.addEventListener('click', function () {
          try { localStorage.removeItem(key); } catch (e) {}
          banner.classList.add('hidden');
          if (status) status.textContent = 'Borrador descartado.';
        });
      }
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setUp);
  } else {
    setUp();
  }
})();