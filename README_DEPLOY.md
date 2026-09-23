# Despliegue gratis — Diálogo y Desarrollo (PHP + MySQL)

## Respuesta corta a tu pregunta
No se puede publicar este proyecto en **GitHub Pages**, ni siquiera con una
base de datos externa gratis. Pages solo sirve archivos estáticos
(HTML/CSS/JS). Tus archivos `front/*.php` y `admin/*.php` necesitan un
servidor PHP que los ejecute, y eso Pages no lo hace (los mostraría como
texto para descargar).

Flujo correcto y gratis:
- **GitHub** = guarda el código y versiona (lo que me pediste con git).
- **Hosting PHP+MySQL gratis** = ejecuta la página. Recomendado: **InfinityFree**.
  Alternativas: Railway, Render + Aiven/PlanetScale.

## Lo que ya dejé preparado
1. `database/dialogoydesarrollo.sql` — dump convertido de **UTF-16 con BOM
   a UTF-8 sin BOM**. Verificado: `Pérez García`, `minería`, `Diálogo`,
   `Boletín` se leen bien. 10 tablas con `CHARSET=utf8mb4
   COLLATE=utf8mb4_unicode_ci`.
2. `front/conexion.php` y `admin/config.php` — ahora leen
   `DB_HOST/DB_NAME/DB_USER/DB_PASS` de variables de entorno con fallback a
   `localhost/root/dialogoydesarrollo`. Agregado
   `PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"`.
3. `.gitignore` — excluye `tailadmin-src/node_modules/`, PDFs/videos subidos
   y los `.sql` viejos en UTF-16 de la raíz.

Archivos viejos `dialogoydesarrollo.sql` y `front/dialogoydesarrollo.sql`
siguen en UTF-16: **no los uses para importar**, usa solo
`database/dialogoydesarrollo.sql`.

## Cómo no romper tildes y Ñ al subir la BD
1. BD destino creada como `utf8mb4_unicode_ci`.
2. Importar el archivo `database/dialogoydesarrollo.sql` (UTF-8 sin BOM).
3. En phpMyAdmin: Importar > Conjunto de caracteres = `utf8mb4`.
   Por línea de comandos: `mysql --default-character-set=utf8mb4`.
4. Verificación post-importe:
   ```sql
   SHOW VARIABLES LIKE 'character_set_database';
   SELECT autor_id, nombre, apellidos FROM autores WHERE autor_id=1;
   -- Debe mostrar: Pérez García, sin ? ni Ã©
   ```

## Qué necesito de ti para publicar
1. Sesión GitHub: ya la tienes activa (`gh auth status` = NICOLAEATS). No
   necesitas hacer nada más.
2. Decide nombre del repo (ej: `dialogo-desarrollo`) y si será público o privado.
3. Crea cuenta gratis en InfinityFree (o dime cuál hosting eliges) y pásame:
   - Host MySQL, nombre BD, usuario, clave (del panel MySQL).
   - Datos FTP (para subir `front/` + `admin/`) o autorízame a guiarte paso a paso.
4. Confirmame para hacer `git init + commit + gh repo create + push`.

## Comandos que ejecutaré cuando confirmes
```powershell
git init; git add .; git commit -m "Deploy inicial"
gh repo create dialogo-desarrollo --public --source=. --push
```
