FROM drupal:11-apache

WORKDIR /opt/drupal

# Nuestro composer.json/lock (versionado en Git) sustituye al del scaffold base,
# de forma que las dependencias del proyecto son reproducibles desde el repo.
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-progress --optimize-autoloader

# Codigo propio versionado: settings.php, services.yml (CORS), modulos/temas custom y configuracion exportada.
COPY web/sites/default/settings.php web/sites/default/settings.php
COPY web/sites/default/services.yml web/sites/default/services.yml
COPY web/modules/custom/ web/modules/custom/
COPY web/themes/custom/ web/themes/custom/
COPY config/sync/ /opt/drupal/config/sync/

# El directorio de ficheros publicos no existe en la imagen base: si no lo creamos
# aqui con el propietario correcto, Docker lo crea como root al montar el volumen
# "drupal-files" por primera vez, y Drupal (que corre como www-data) no puede escribir
# en el (cache de PHP/Twig, imagenes subidas, estilos de imagen, etc.).
RUN mkdir -p web/sites/default/files \
    && chown -R www-data:www-data web/sites/default/files
