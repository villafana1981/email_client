<!DOCTYPE HTML>
<html lang="es">
<head>
<style>
 .left {
	float: left;
    width: 20%;
    text-align:right;
    vertical-align: top;
}
.right {
	overflow: hidden;
}
</style>
<title>Email</title>
</head>
<body>
<h1>Email</h1>
<ul class="errors">
@foreach($errors->all() as $message)
       <li>{{ $message }}</li>
@endforeach
</ul>

{{Form::open(array('action'=>'EmailController@process','method'=>'post','files' => true, 'id'=>'emailForm'))}}
	{{Form::hidden('actionUrl',(URL::to('/').'/email/'),array('id'=>'actionUrl'))}}
	{{Form::hidden('composerId',isset($composerId)?$composerId:Input::old('composerId'),array('id'=>'composerId'))}}
	<span class="left">{{Form::label('to','*To:')}}</span><span class="right">{{Form::text('to','')}}</span>
    <br>
    <span class="left">{{Form::label('subject','*Subject:')}}</span><span class="right">{{Form::text('subject', Input::old('subject'))}}</span>
    <br>
    <span class="left">{{Form::label('cc','Cc:')}}</span><span class="right">{{Form::text('cc')}}</span>
    <br>
    <span class="left">{{Form::label('bcc','Bcc:')}}</span><span class="right">{{Form::text('bcc')}}</span>
    <br>
    <span class="left">{{Form::label('body','Message:')}}</span><span class="right">{{Form::textarea('body','Contenido')}}</span>
    <br>
    <br>
	<span class="left" >Archivos:</span><span class="right">@include('emails.upload')</span>
	<br>
    <span class="left" >*</span><span class="right">{{Form::submit('Enviar', array('name'=>'sendEmailButton'),array('class','start'))}}</span>
{{Form::close()}}
<br>
</body> 
</html>
