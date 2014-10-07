<!-- Force latest IE rendering engine or ChromeFrame if installed -->
<!--[if IE]>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<![endif]-->
<meta charset="utf-8">

<!-- jQuery UI styles-->
<!--<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/themes/dark-hive/jquery-ui.css" id="theme">-->
<link rel="stylesheet" href="{{URL::to('/')}}/css/jquery-ui.css" id="theme">
<!--<link rel="stylesheet" href="css/jquery-ui.css" id="theme">-->
<!-- Demo styles 
<link rel="stylesheet" href="css/demo.css">
-->
<!--[if lte IE 8]>
<link rel="stylesheet" href="css/demo-ie8.css">
<![endif]-->
<style>
/* Adjust the jQuery UI widget font-size: */
.ui-widget {
    font-size: 0.95em;
}
</style>
<!-- blueimp Gallery styles 
<link rel="stylesheet" href="//blueimp.github.io/Gallery/css/blueimp-gallery.min.css">-->
<link rel="stylesheet" href="{{URL::to('/')}}/css/blueimp/blueimp-gallery.min.css">
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="{{URL::to('/')}}/css/jquery.fileupload.css">
<link rel="stylesheet" href="{{URL::to('/')}}/css/jquery.fileupload-ui.css">
<!-- CSS adjustments for browsers with JavaScript disabled -->


<div class="fileupload-buttonbar">
        <div class="fileupload-buttons">
            <!-- The fileinput-button span is used to style the file input field as button -->
            <span class="fileinput-button">
                <span>Adjuntar</span>
                <input type="file" name="files[]" multiple>
            </span>
            <span class="fileupload-process"></span>
        </div>
        <div class="fileupload-progress fade" style="display:none">
            <!-- The global progress bar -->
            <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
            <!-- The extended global progress state -->
            <div class="progress-extended">&nbsp;</div>
        </div>
    </div>
    <!-- The table listing the files available for upload/download -->
    <table role="presentation"><tbody class="files"></tbody></table>
    
    <!-- The blueimp Gallery widget -->
<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <a class="prev">Anterior</a>
    <a class="next">Siguiente</a>
    <a class="close">Cerrar</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress"></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="start" disabled>Subir</button>
            {% } %}
            {% if (!i) { %}
                <button class="cancel">Cancelar</button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <td>
            <p class="name">
                {%=file.name%}
            </p>
            {% if (file.error) { %}
                <div><span class="error">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            <button class="delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>Eliminar</button>
            <input type="checkbox" name="delete" value="1" class="toggle" checked='checked' style='display:none'>
        </td>
    </tr>
{% } %}
</script>
<!--  
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
<!-- The Templates plugin is included to render the upload/download listings 
<script src="//blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality 
<script src="//blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality 
<script src="//blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
<!-- blueimp Gallery script 
<script src="//blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
-->
<script src="{{URL::to('/')}}/js/jquery.min.js"></script>
<script src="{{URL::to('/')}}/js/jquery-ui.min.js"></script>
<!-- The Templates plugin is included to render the upload/download listings --> 
<script src="{{URL::to('/')}}/js/blueimp/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality --> 
<script src="{{URL::to('/')}}/js/blueimp/load-image.all.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="{{URL::to('/')}}/js/blueimp/canvas-to-blob.min.js"></script>
<!-- blueimp Gallery script -->
<script src="{{URL::to('/')}}/js/blueimp/jquery.blueimp-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="{{URL::to('/')}}/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="{{URL::to('/')}}/js/jquery.fileupload.js"></script>
<!-- The File Upload processing plugin -->
<script src="{{URL::to('/')}}/js/jquery.fileupload-process.js"></script>
<!-- The File Upload image preview & resize plugin -->
<script src="{{URL::to('/')}}/js/jquery.fileupload-image.js"></script>
<!-- The File Upload audio preview plugin -->
<script src="{{URL::to('/')}}/js/jquery.fileupload-audio.js"></script>
<!-- The File Upload video preview plugin -->
<script src="{{URL::to('/')}}/js/jquery.fileupload-video.js"></script>
<!-- The File Upload validation plugin -->
<script src="{{URL::to('/')}}/js/jquery.fileupload-validate.js"></script>
<!-- The File Upload user interface plugin -->
<script src="{{URL::to('/')}}/js/jquery.fileupload-ui.js"></script>
<!-- The File Upload jQuery UI plugin -->
<script src="{{URL::to('/')}}/js/jquery.fileupload-jquery-ui.js"></script>
<!-- The main application script -->
<script src="{{URL::to('/')}}/js/main.js"></script>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
<!--[if (gte IE 8)&(lt IE 10)]>
<script src="js/cors/jquery.xdr-transport.js"></script>
<![endif]-->

<!-- </form>-->
