| 1.  | Requisitos |     | de  | interfaz |     |     |     |     |     |
| --- | ---------- | --- | --- | -------- | --- | --- | --- | --- | --- |
El diseño debe ser adaptable, de modo que pueda visualizarse correctamente tanto
|     | en un | monitor | o portátil | como | en una | tableta | o   | un móvil. |     |
| --- | ----- | ------- | ---------- | ---- | ------ | ------- | --- | --------- | --- |
El sitio debe ser accesible. Puede validarse con alguna de las herramientas disponi-
|     | bles en   | https://www.w3.org/WAI/test-evaluate/tools/list/. |               |     |        |          |     |           |     |
| --- | --------- | ------------------------------------------------- | ------------- | --- | ------ | -------- | --- | --------- | --- |
|     | El diseño | debe                                              | ser coherente |     | con la | temática | del | proyecto. |     |
La interfaz debe ofrecer un menú superior, una zona central de contenido y un menú
|     | lateral | coherente | con | la web. |     |     |     |     |     |
| --- | ------- | --------- | --- | ------- | --- | --- | --- | --- | --- |
Toda la información de presentación deberá estar en ficheros CSS, manteniendo una
|     | buena | separación | entre | HTML | y estilos. |     |     |     |     |
| --- | ----- | ---------- | ----- | ---- | ---------- | --- | --- | --- | --- |
Todos los documentos del sitio deberán compartir la misma cabecera y el mismo pie
|     | de página. |     |     |     |     |     |     |     |     |
| --- | ---------- | --- | --- | --- | --- | --- | --- | --- | --- |
El pie de página deberá incluir enlaces al documento contacto.php, con los datos
deldesarrollador,yalficheroPDFconelinformedelapráctica,como_se_hizo.pdf.
Todos los formularios requeridos deberán estar diseñados en HTML5, sin JavaScript
específico. Todos los campos serán obligatorios, por lo que tendrán que realizarse
|     | las comprobaciones |     | oportunas. |     |     |     |     |     |     |
| --- | ------------------ | --- | ---------- | --- | --- | --- | --- | --- | --- |
Se deberá diseñar la base de datos adecuada para cada proyecto.
| 2.  | Requisitos |     | de  | funcionamiento |     |     |     | dinámico |     |
| --- | ---------- | --- | --- | -------------- | --- | --- | --- | -------- | --- |
Deberán existir tres tipos de usuario: usuario no identificado, usuario normal y
|     | usuario | con permisos |     | especiales. |     |     |     |     |     |
| --- | ------- | ------------ | --- | ----------- | --- | --- | --- | --- | --- |
Los usuarios podrán identificarse y mantener activa una sesión hasta que la cierren.
Una vez identificado, deberá aparecer su nombre de usuario y su tipo en la esquina
superior derecha, junto con un enlace o botón para cerrar sesión.
1

Los usuarios identificados deberán ver información específica de su cuenta a la que
solo ellos puedan acceder, como por ejemplo su carrito de la compra.
Debe haber formularios de entrada con una validación adecuada de los datos.
| 3.7. | Intercambio | de  | Libros de Segunda | Mano |
| ---- | ----------- | --- | ----------------- | ---- |
Se propone una plataforma de economía circular dedicada al intercambio de libros entre
usuarios. Más que una tienda, se plantea como una red de lectura donde los usuarios
pueden ofrecer sus libros, buscar títulos específicos por autor o categoría y gestionar el
proceso de intercambio mediante un sistema de solicitudes y aceptaciones.
Vista pública: Buscador de libros disponibles por título, autor o categoría.
| Área | de usuario: |     |     |     |
| ---- | ----------- | --- | --- | --- |
Publicación de libros propios y gestión de solicitudes de intercambio
|      |                   | mediante | un buzón. |     |
| ---- | ----------------- | -------- | --------- | --- |
| Área | de administrador: |          |           |     |
Gestión de las categorías del sitio y resolución de disputas entre usua-
rios.
| Páginas | mínimas: |     |     |     |
| ------- | -------- | --- | --- | --- |
Catálogo de libros, detalle del libro, publicación de libro, perfil de
usuario (mis libros y buzón), administración de categorías y contacto.
| Validación | clave: |     |     |     |
| ---------- | ------ | --- | --- | --- |
Validación de campos obligatorios y consistencia de datos en el catá-
logo.

