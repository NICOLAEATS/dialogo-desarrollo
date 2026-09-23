<?php
/**
 * Definiciones declarativas de las tablas de la base de datos dialogoydesarrollo.
 * Se usan tanto en crud.php (guardado) como en las paginas de listado/formulario.
 */

function definiciones_tablas()
{
    return [
        'usuarios' => [
            'pk' => 'user_id',
            'pagina' => 'usuarios.php',
            'titulo' => 'Usuario',
            'singulares' => true,
            'campos' => [
                'nombre_completo' => ['tipo' => 'text', 'req' => true, 'label' => 'Nombre completo'],
                'email' => ['tipo' => 'email', 'req' => true, 'label' => 'Correo electronico'],
                'password' => ['tipo' => 'password', 'req' => false, 'label' => 'Contrasena'],
                'telefono' => ['tipo' => 'text', 'req' => false, 'label' => 'Telefono'],
                'avatar_url' => ['tipo' => 'text', 'req' => false, 'label' => 'URL avatar'],
                'rol' => ['tipo' => 'select', 'opciones' => ['admin' => 'admin', 'editor' => 'editor', 'autor' => 'autor'], 'req' => false, 'label' => 'Rol'],
                'activo' => ['tipo' => 'checkbox', 'req' => false, 'label' => 'Cuenta activa'],
            ],
        ],
        'autores' => [
            'pk' => 'autor_id',
            'pagina' => 'autores.php',
            'titulo' => 'Autor',
            'campos' => [
                'user_id' => ['tipo' => 'select', 'fuente' => 'usuarios', 'req' => false, 'label' => 'Vinculado a usuario'],
                'nombre' => ['tipo' => 'text', 'req' => true, 'label' => 'Nombre'],
                'apellidos' => ['tipo' => 'text', 'req' => false, 'label' => 'Apellidos'],
                'biografia' => ['tipo' => 'textarea', 'req' => false, 'label' => 'Biografia'],
                'foto_url' => ['tipo' => 'text', 'req' => false, 'label' => 'URL foto'],
                'activo' => ['tipo' => 'checkbox', 'req' => false, 'label' => 'Visible en la web'],
            ],
        ],
        'reportajes' => [
            'pk' => 'reportaje_id',
            'pagina' => 'reportajes.php',
            'titulo' => 'Reportaje',
            'campos' => [
                'titulo' => ['tipo' => 'text', 'req' => true, 'label' => 'Titulo'],
                'resumen_corto' => ['tipo' => 'textarea', 'req' => false, 'label' => 'Resumen corto'],
                'desarrollo' => ['tipo' => 'textarea', 'req' => false, 'label' => 'Desarrollo'],
                'foto_portada_url' => ['tipo' => 'text', 'req' => false, 'label' => 'URL foto de portada'],
                'archivo_pdf_url' => ['tipo' => 'text', 'req' => false, 'label' => 'URL PDF (infografia)'],
                'fecha_publicacion' => ['tipo' => 'date', 'req' => false, 'label' => 'Fecha de publicacion'],
                'es_destacado' => ['tipo' => 'checkbox', 'req' => false, 'label' => 'Es destacado'],
                'autor_id' => ['tipo' => 'select', 'fuente' => 'autores', 'req' => true, 'label' => 'Autor'],
                'usuario_id' => ['tipo' => 'select', 'fuente' => 'usuarios', 'req' => true, 'label' => 'Publicado por'],
                'activo' => ['tipo' => 'checkbox', 'req' => false, 'label' => 'Visible en la web'],
                'estado' => ['tipo' => 'select', 'opciones' => ['publicado' => 'Publicado', 'borrador' => 'Borrador'], 'req' => false, 'label' => 'Estado'],
            ],
        ],
        'reportajes_fotos' => [
            'pk' => 'foto_id',
            'pagina' => 'reportajes_fotos.php',
            'titulo' => 'Foto de Reportaje',
            'campos' => [
                'reportaje_id' => ['tipo' => 'select', 'fuente' => 'reportajes', 'req' => true, 'label' => 'Reportaje'],
                'url_foto' => ['tipo' => 'text', 'req' => true, 'label' => 'URL de la foto'],
                'orden' => ['tipo' => 'number', 'req' => false, 'label' => 'Orden'],
                'descripcion' => ['tipo' => 'text', 'req' => false, 'label' => 'Descripcion'],
                'es_principal' => ['tipo' => 'checkbox', 'req' => false, 'label' => 'Es foto principal'],
                'activo' => ['tipo' => 'checkbox', 'req' => false, 'label' => 'Visible en la web'],
            ],
        ],
        'noticias' => [
            'pk' => 'noticia_id',
            'pagina' => 'noticias.php',
            'titulo' => 'Noticia',
            'campos' => [
                'titulo' => ['tipo' => 'text', 'req' => true, 'label' => 'Titulo'],
                'resumen' => ['tipo' => 'textarea', 'req' => false, 'label' => 'Resumen'],
                'foto_url' => ['tipo' => 'text', 'req' => false, 'label' => 'URL foto'],
                'link_externo' => ['tipo' => 'text', 'req' => false, 'label' => 'Link externo'],
                'fecha_publicacion' => ['tipo' => 'date', 'req' => false, 'label' => 'Fecha de publicacion'],
                'es_destacado' => ['tipo' => 'checkbox', 'req' => false, 'label' => 'Destacado (aparece arriba del todo en la portada)'],
                'usuario_id' => ['tipo' => 'select', 'fuente' => 'usuarios', 'req' => true, 'label' => 'Publicada por'],
                'activo' => ['tipo' => 'checkbox', 'req' => false, 'label' => 'Visible en la web'],
                'estado' => ['tipo' => 'select', 'opciones' => ['publicado' => 'Publicado', 'borrador' => 'Borrador'], 'req' => false, 'label' => 'Estado'],
            ],
        ],
        'boletines' => [
            'pk' => 'boletin_id',
            'pagina' => 'boletines.php',
            'titulo' => 'Boletin',
            'campos' => [
                'numero_boletin' => ['tipo' => 'number', 'req' => true, 'label' => 'Numero de boletin'],
                'titulo' => ['tipo' => 'text', 'req' => false, 'label' => 'Titulo'],
                'resumen' => ['tipo' => 'textarea', 'req' => false, 'label' => 'Resumen'],
                'foto_portada_url' => ['tipo' => 'text', 'req' => false, 'label' => 'URL foto de portada'],
                'archivo_pdf_url' => ['tipo' => 'text', 'req' => false, 'label' => 'URL PDF del boletin'],
                'fecha_publicacion' => ['tipo' => 'date', 'req' => false, 'label' => 'Fecha de publicacion'],
                'usuario_id' => ['tipo' => 'select', 'fuente' => 'usuarios', 'req' => true, 'label' => 'Creado por'],
                'activo' => ['tipo' => 'checkbox', 'req' => false, 'label' => 'Visible en la web'],
                'estado' => ['tipo' => 'select', 'opciones' => ['publicado' => 'Publicado', 'borrador' => 'Borrador'], 'req' => false, 'label' => 'Estado'],
            ],
        ],
        'podcasts' => [
            'pk' => 'podcast_id',
            'pagina' => 'podcasts.php',
            'titulo' => 'Podcast',
            'campos' => [
                'titulo' => ['tipo' => 'text', 'req' => true, 'label' => 'Titulo'],
                'descripcion' => ['tipo' => 'textarea', 'req' => false, 'label' => 'Descripcion'],
                'archivo_url' => ['tipo' => 'text', 'req' => false, 'label' => 'Subir audio desde el PC (se reproduce embebido)'],
                'url_embed' => ['tipo' => 'text', 'req' => false, 'label' => 'Opcional: enlace externo (YouTube/Stream)'],
                'duracion_segundos' => ['tipo' => 'number', 'req' => false, 'label' => 'Duracion (segundos)'],
                'imagen_url' => ['tipo' => 'text', 'req' => false, 'label' => 'URL imagen'],
                'fecha_publicacion' => ['tipo' => 'date', 'req' => false, 'label' => 'Fecha de publicacion'],
                'usuario_id' => ['tipo' => 'select', 'fuente' => 'usuarios', 'req' => true, 'label' => 'Publicado por'],
                'activo' => ['tipo' => 'checkbox', 'req' => false, 'label' => 'Visible en la web'],
                'estado' => ['tipo' => 'select', 'opciones' => ['publicado' => 'Publicado', 'borrador' => 'Borrador'], 'req' => false, 'label' => 'Estado'],
            ],
        ],
        'videos' => [
            'pk' => 'video_id',
            'pagina' => 'videos.php',
            'titulo' => 'Video',
            'campos' => [
                'titulo' => ['tipo' => 'text', 'req' => true, 'label' => 'Titulo'],
                'descripcion' => ['tipo' => 'textarea', 'req' => false, 'label' => 'Descripcion'],
                'archivo_url' => ['tipo' => 'text', 'req' => false, 'label' => 'Subir video desde el PC (se reproduce embebido)'],
                'url_embed' => ['tipo' => 'text', 'req' => false, 'label' => 'Opcional: enlace externo (YouTube/Stream)'],
                'duracion_segundos' => ['tipo' => 'number', 'req' => false, 'label' => 'Duracion (segundos)'],
                'imagen_url' => ['tipo' => 'text', 'req' => false, 'label' => 'URL imagen'],
                'fecha_publicacion' => ['tipo' => 'date', 'req' => false, 'label' => 'Fecha de publicacion'],
                'usuario_id' => ['tipo' => 'select', 'fuente' => 'usuarios', 'req' => true, 'label' => 'Publicado por'],
                'activo' => ['tipo' => 'checkbox', 'req' => false, 'label' => 'Visible en la web'],
                'estado' => ['tipo' => 'select', 'opciones' => ['publicado' => 'Publicado', 'borrador' => 'Borrador'], 'req' => false, 'label' => 'Estado'],
            ],
        ],
        'pdfs' => [
            'pk' => 'pdf_id',
            'pagina' => 'pdfs.php',
            'titulo' => 'Documento PDF',
            'campos' => [
                'titulo' => ['tipo' => 'text', 'req' => true, 'label' => 'Titulo'],
                'url_archivo' => ['tipo' => 'text', 'req' => true, 'label' => 'URL del archivo'],
                'descripcion' => ['tipo' => 'textarea', 'req' => false, 'label' => 'Descripcion'],
                'usuario_id' => ['tipo' => 'select', 'fuente' => 'usuarios', 'req' => true, 'label' => 'Subido por'],
                'activo' => ['tipo' => 'checkbox', 'req' => false, 'label' => 'Visible en la web'],
            ],
        ],
        'fotos' => [
            'pk' => 'foto_id',
            'pagina' => 'fotos.php',
            'titulo' => 'Foto',
            'campos' => [
                'titulo' => ['tipo' => 'text', 'req' => false, 'label' => 'Titulo'],
                'url_foto' => ['tipo' => 'text', 'req' => true, 'label' => 'URL de la foto'],
                'descripcion' => ['tipo' => 'textarea', 'req' => false, 'label' => 'Descripcion'],
                'fecha_publicacion' => ['tipo' => 'date', 'req' => false, 'label' => 'Fecha de publicacion'],
                'usuario_id' => ['tipo' => 'select', 'fuente' => 'usuarios', 'req' => true, 'label' => 'Subida por'],
                'activo' => ['tipo' => 'checkbox', 'req' => false, 'label' => 'Visible en la web'],
            ],
        ],
    ];
}

/**
 * Columna de texto que muestra una relacion en el listado.
 */
function columna_relacion($fuente)
{
    switch ($fuente) {
        case 'usuarios':
            return 'nombre_completo';
        case 'autores':
            return "CONCAT(nombre,' ',apellidos)";
        case 'reportajes':
            return 'titulo';
    }
    return 'titulo';
}

/**
 * Devuelve la lista de opciones de un campo tipo select.
 */
function opciones_select($fuente)
{
    global $pdo;
    switch ($fuente) {
        case 'usuarios':
            $rows = $pdo->query('SELECT user_id, nombre_completo, rol, activo FROM usuarios ORDER BY nombre_completo')->fetchAll();
            $opciones = [];
            foreach ($rows as $r) {
                $opciones[$r['user_id']] = $r['nombre_completo'] . ' (' . $r['rol'] . ($r['activo'] ? '' : ' · inactivo') . ')';
            }
            return $opciones;
        case 'autores':
            $rows = $pdo->query('SELECT autor_id, nombre, apellidos FROM autores ORDER BY nombre')->fetchAll();
            $opciones = [];
            foreach ($rows as $r) {
                $opciones[$r['autor_id']] = trim($r['nombre'] . ' ' . $r['apellidos']);
            }
            return $opciones;
        case 'reportajes':
            $rows = $pdo->query('SELECT reportaje_id, titulo FROM reportajes ORDER BY titulo')->fetchAll();
            $opciones = [];
            foreach ($rows as $r) {
                $opciones[$r['reportaje_id']] = $r['titulo'];
            }
            return $opciones;
    }
    return [];
}