/* ===== Editor tipo Word (CKEditor) para el panel admin =====
 * Convierte el textarea #wysiwyg_hidden_desarrollo en un editor completo
 * (negrita, subrayado, colores, listas, tablas, enlaces, etc.).
 * Mantiene sincronizado el textarea oculto para el autoguardado.
 */
(function () {
  var EDITOR_ID = 'wysiwyg_hidden_desarrollo';

  function crearEditor() {
    var textarea = document.getElementById(EDITOR_ID);
    if (!textarea || !window.CKEDITOR) return false;

    var editor = CKEDITOR.replace(textarea, {
      height: 420,
      uiColor: null,
      language: 'es',
      toolbarGroups: [
        { name: 'clipboard', groups: ['undo', 'clipboard'] },
        { name: 'basicstyles', groups: ['basicstyles'] },
        { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'] },
        { name: 'links' },
        { name: 'insert' },
        { name: 'styles' },
        { name: 'colors' },
        { name: 'tools' },
        { name: 'document', groups: ['mode', 'document', 'tools'] }
      ],
      toolbar: [
        { name: 'deshacer', items: ['Undo', 'Redo'] },
        { name: 'formato', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'RemoveFormat'] },
        { name: 'fuente', items: ['Font', 'FontSize'] },
        { name: 'color', items: ['TextColor', 'BGColor'] },
        { name: 'estilos', items: ['Format'] },
        { name: 'alinear', items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
        { name: 'listas', items: ['NumberedList', 'BulletedList', 'Outdent', 'Indent'] },
        { name: 'cita', items: ['Blockquote'] },
        { name: 'enlace', items: ['Link', 'Unlink', 'Anchor'] },
        { name: 'insertar', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar'] },
        { name: 'herramientas', items: ['Source', 'ShowBlocks', 'Maximize'] }
      ],
      allowedContent: true,
      entities: false,
      autoParagraph: false,
      removePlugins: 'easyimage,imagebase,exportpdf'
    });

    // Mantener el textarea al dia (guarda + autoguardado + submit).
    editor.on('change', function () {
      editor.updateElement();
      if (textarea.dispatchEvent) textarea.dispatchEvent(new Event('input', { bubbles: true }));
    });
    var form = textarea.closest ? textarea.closest('form') : null;
    if (form) {
      form.addEventListener('submit', function () {
        try { editor.updateElement(); } catch (e) {}
      });
      form.addEventListener('pagehide', function () {
        try { editor.updateElement(); } catch (e) {}
      });
    }
    return true;
  }

  function inicializar() {
    if (!document.getElementById(EDITOR_ID)) return;
    if (window.CKEDITOR) {
      try { crearEditor(); } catch (e) { if (window.console) console.error('CKEditor:', e); }
    } else {
      setTimeout(inicializar, 250);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializar);
  } else {
    inicializar();
  }
})();