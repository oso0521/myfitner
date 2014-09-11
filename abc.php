<?php	
	include("header.php");	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Admin</title>
        <link rel="stylesheet" href="_ABC/css/red-theme/jquery-ui.min.css" />
        <link rel="stylesheet" href="_ABC/css/red-theme/jquery-ui.theme.min.css" />
        <link rel="stylesheet" href="_ABC/css/jquery.tooltip.css" />
        <link rel="stylesheet" href="_ABC/css/ajaxfileupload.css" />
        <link rel="stylesheet" href="_ABC/css/jqueryui.datatables.css" />
        <link rel="stylesheet" href="_ABC/css/abc_style.css" />
         <script type="text/javascript" src="_ABC/js/jquery-1.11.1.min.js"></script>
		<script type="text/javascript" src="_ABC/js/jquery-ui.1.10.min.js"></script>
        <script type="text/javascript" src="_ABC/js/jquery-ui-timepicker-addon.js"></script>
        <script type="text/javascript" src="_ABC/js/jquery.imagepreview.js"></script>
        <script type="text/javascript" src="_ABC/js/abc_function.js"></script>
        <script type="text/javascript" src="_ABC/js/ajaxfileupload.js"></script>
        <script type="text/javascript" src="_ABC/js/abc_ajax.js"></script>
        <script type="text/javascript" src="_ABC/js/jquery.dataTables.js"></script>
        <script type="text/javascript" src="_ABC/js/datatables.jquery.integration.js"></script>
        <script type="text/javascript" src="_ABC/js/jquery.noty.packaged.min.js"></script>
        <style>
			.inline{
				display:inline-block;				
			}
			.w33
			{
				width:30%;				
			}
		</style>
    </head>
    <body>
    	
        <table id="ejercicio">
        </table>
        <script>
		
			
			abc_table("Ejercicio","file_img:../../img/static,_IMAGE|file_gif:../../gifs,_IMAGE","ejercicio");
		</script>
    </body>
</html>