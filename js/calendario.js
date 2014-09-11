// JavaScript Document
var DOMAIN = "http://myfitner.movitbe.com/";
var USER_ID = "";


var mesesTreinta = new Array(4,6,9,11);
var mesesTreuntaUno = new Array(1,3,5,7,8,10,12);
var dias = new Array('Dom','Lun','Mar','Mie','Jue','Vie','Sab');
var meses = new Array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
var fecha = new Date();
var febrero=0;

if(es_bisiesto(fecha.getFullYear()))
{
	febrero = 29;	
}else
{	
	febrero = 28;
}

function getDiaSemana(Ano,Mes,Dia)
{
	var a = Math.floor((14 - Mes) / 12);
	var y = Ano - a;
	var m = Mes + 12 * a - 2;
	
	var d = (Dia + y + Math.floor(y/4) - Math.floor(y/100) + Math.floor(y/400) + Math.floor((31 * m) /12)) % 7;
	
	return d;
	//El resultado es un cero (0) para el domingo, 1 para el lunesâ€¦ 6 para el sÃ¡bado	
}

function es_bisiesto(year){
	return ((year % 4 == 0 && year % 100 != 0) || year % 400 == 0) ? true : false;
}

function date(d)
{
		
	var date;
	if( typeof d !== 'undefined')
	{
		auxArrDate = d.split("-");
		date = new Date(auxArrDate[0],auxArrDate[1],auxArrDate[2]);
	}else
	{
		date = new Date();	
	}
		
	var diaSemana = date.getDay();
	var dia = date.getDate();
	var mes = date.getMonth();
	var anio = date.getFullYear();
	var html = '<table class="calendario" cellspacing="0" >';
	html += '<thead>';	
		html += '<tr>';
			html += '<th class="titulo" colspan="7" >';
				html += meses[mes]+" "+anio;
			html += '</th>';
		html += '</tr>';
		html += '<tr class="dias">';
		//Carga los headers de los días de la semana
		$.each(dias,function(i,elem){
			html += '<td>'+elem+'</td>';
		});
		html += '</tr>';
	html += '</thead>';
	html += '<tbody>';
		html += '<tr>';
		var fechaRegresiva = new Date(anio,mes,1);
		var diaSemanaAux = fechaRegresiva.getDay();
		fechaRegresiva.setDate(fechaRegresiva.getDate() -1 );
		console.log(fechaRegresiva);
		var j= 0;
		/*
		Primer renglón del calendario
		*/
		for(diaSemanaAux-1;diaSemanaAux>0;diaSemanaAux--)
		{
			j++;
			html += '<td><div class="numDia">'+(fechaRegresiva.getDate()-(diaSemanaAux-1))+'</div></td>';	
		}		
		for(i=j;i<7;i++)
		{
			fechaRegresiva.setDate(fechaRegresiva.getDate()+1);
			html += '<td><div class="numDia">'+fechaRegresiva.getDate()+'</div></td>';	
			
		}
		html += '</tr>';
		/*
		Fin primer renglón del calendario
		*/
		
		for(i=0;i<4;i++)
		{
			html += '<tr>';
			for(j=0;j<7;j++)
			{
				fechaRegresiva.setDate(fechaRegresiva.getDate()+1);
				html += '<td><div class="numDia">'+fechaRegresiva.getDate()+'</div></td>';	
			}
			html += '</tr>';
		}
	html += '</tbody>';	
	html += '</table>';
	
	$(".calendario").html(html);
}

function getUserId()
{
	if($.isNumeric(USER_ID))
	{
		return USER_ID;	
	}else
	{
		$.get(DOMAIN+"responses/getUserId.php",function(e){
			
		}).fail(function(){
			console.log("Fail de getUserID");
		});
	}	
}

function getEntrenos()
{
	try
	{		
		$.get(DOMAIN+"/responses/actualizaEntrenos.php?",function(e){
			
		}).fail(function(){
			console.log("Fail de getEntrenos");
		});
	}catch(e){}
}


$(document).ready(function(e) {
	date();  
	getEntrenos();
	getUserId();
});



function getPantallaEntrenamientos()
{
	$.get("responses/planesEntrenamiento.html",function(e){
		$("#contenido").html(e);
	});	
}

/***

Formula para calcular el peso ideal Pesas

1/30( 30 + repeticiones ) * pesoCargado

** peso cargado tiene que estar en libras

**************************2*********************

Calorias





***/

function getQueryVariable(variable)
{
       var query = window.location.search.substring(1);
       var vars = query.split("&");
       for (var i=0;i<vars.length;i++) {
               var pair = vars[i].split("=");
               if(pair[0] == variable){return pair[1];}
       }
       return(false);
}
var ID_RUTINA=null;
var ID_PLAN = null;
function getPlanPesas(idRutina,idPlan)
{
	ID_RUTINA = idRutina;
	ID_PLAN = idPlan;
	$.get("responses/planesPesas.html",function(e){
		$("#contenido").html(e);
	});		
}
var ID_ESPECIALIDAD=null;
var ID_PROGRAMA=null;
function getPlanHome(id)
{
	ID_ESPECIALIDAD = id;
	$.get("responses/programas.html?idEspecialidad="+id,function(e){
		$("#contenido").html(e);
	});	
}

function getPlanesEspecialidad(idPrograma, idEspecialidad)
{
	ID_PROGRAMA = idPrograma;
	ID_ESPECIALIDAD = idEspecialidad;
	$.get("responses/homePlanes.html",function(e){
		$("#contenido").html(e);	
	});	
}
var BASE_JSON={"data":[]};
function getData()
{
	try
	{
		$.getJSON(DOMAIN+"/getPlanes.php",function(data){
			//console.log(data);
			if(data != "" && data != null )
					BASE_JSON['data'] = data['Data'];
				
			$.each(data['Data'],function(i,elem){
				
			}); 
				window.localStorage.setItem("Data",JSON.stringify(BASE_JSON) );
		});
	}catch(e){}
}