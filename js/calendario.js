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
		//console.log(fechaRegresiva);
		var j= 0;
		/*
		Primer renglón del calendario
		*/
		for(diaSemanaAux-1;diaSemanaAux>0;diaSemanaAux--)
		{
			j++;
			html += '<td class="'+(fechaRegresiva.getDate() == dia && fechaRegresiva.getMonth() == mes ? "hoy":"")+'"><div class="numDia ">'+(fechaRegresiva.getDate()-(diaSemanaAux-1))+'</div></td>';	
		}		
		for(i=j;i<7;i++)
		{
			fechaRegresiva.setDate(fechaRegresiva.getDate()+1);
			html += '<td class="'+(fechaRegresiva.getDate() == dia && fechaRegresiva.getMonth() == mes ? "hoy":"")+'"><div class="numDia ">'+fechaRegresiva.getDate()+'</div></td>';	
			
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
				html += '<td class="'+(fechaRegresiva.getDate() == dia && fechaRegresiva.getMonth() == mes ? "hoy":"")+'"><div class="numDia">'+fechaRegresiva.getDate()+'</div></td>';	
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
var ID_ESPECIALIDAD=null;
var ID_PROGRAMA=null;
function getPlanPesas(idRutina,idPlan,elem)
{
	ID_RUTINA = idRutina;
	ID_PLAN = idPlan;
	ID_ESPECIALIDAD = 1;
	$.get("responses/calendarizaPlan.html",function(e){
		elem.parent().parent().parent().parent().parent().html(e);
	});		
}

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
	
	if(idEspecialidad == 2)
	{
		$.get("responses/homePlanesCardio.html",function(e){
			$("#contenido").html(e);	
		});	
	}
	else
	{
		$.get("responses/homePlanes.html",function(e){
			$("#contenido").html(e);	
		});	
	}
}

function getDetalleRutina(idEs,idPr,idPl,idRuPl)
{
	ID_ESPECIALIDAD = idEs;
	ID_PROGRAMA = idPr;
	ID_PLAN = idPl;
	ID_RUTINAPLAN = idRuPl;
	
	if(idEs == 2)
	{
		$.get("responses/detalleRutinaCardio.html",function(e){
			$("#contenido").html(e);	
		});
	}
	else
	{
		//sth sth
	}
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
			
			//console.log(BASE_JSON);	
			$.each(data['Data'],function(i,elem){
				
			}); 
				window.localStorage.setItem("Data",JSON.stringify(BASE_JSON) );
		});
	}catch(e){}
}

//GENERA HTML DE LA GRAFICA
function generaGrafica(ejercicios)
{
	var nBarras = Object.keys(ejercicios).length;
	//nBarras = 7;
	var wPadre = nBarras*20;
	var wHijo = 100/nBarras;
	
	var resultado = "<div style='width:80%; overflow-x:auto; overflow-y:hidden; margin-left:10%; margin-top:10%; text-align:left;'><div class='grafica' style='width:"+wPadre+"%'>";
	
	$.each(ejercicios,function(i,ejercicio)
	{
		switch(ejercicio['ritmo'])
		{
			case '1':
				resultado += "<div class='intensidad1' style='width:"+wHijo+"%'><div class='area'><div style='height: 100%; display: inline-block; width: 0px;'></div><div class='barra'></div></div><div class='tag'>"+ejercicio['duracion']+"</div></div>";
			break;
			case '2':
				resultado += "<div class='intensidad2' style='width:"+wHijo+"%'><div class='area'><div style='height: 100%; display: inline-block; width: 0px;'></div><div class='barra'></div></div><div class='tag'>"+ejercicio['duracion']+"</div></div>";
			break;
			case '3':
				resultado += "<div class='intensidad3' style='width:"+wHijo+"%'><div class='area'><div style='height: 100%; display: inline-block; width: 0px;'></div><div class='barra'></div></div><div class='tag'>"+ejercicio['duracion']+"</div></div>";
			break;
			case '4':
				resultado += "<div class='intensidad4' style='width:"+wHijo+"%'><div class='area'><div style='height: 100%; display: inline-block; width: 0px;'></div><div class='barra'></div></div><div class='tag'>"+ejercicio['duracion']+"</div></div>";
			break;
			case '5':
				resultado += "<div class='intensidad5' style='width:"+wHijo+"%'><div class='area'><div style='height: 100%; display: inline-block; width: 0px;'></div><div class='barra'></div></div><div class='tag'>"+ejercicio['duracion']+"</div></div>";
			break; 
			default:
				resultado += "algo salio mal";
			break
		}
	});
	
	resultado += "</div></div>";
		
	//resultado = "<div style='width: 80%; overflow-x: scroll; overflow-y: hidden; margin-left:10%;'><div class='grafica' style='width:"+wPadre+"%'><div class='intensidad1' style='width:"+wHijo+"%'><div class='area'><div style='height: 100%; display: inline-block; width: 0px;'></div><div class='barra'></div></div><div class='tag'>00:00:00</div></div><div class='intensidad2' style='width:"+wHijo+"%'><div class='area'><div style='height: 100%; display: inline-block; width: 0px;'></div><div class='barra'></div></div><div class='tag'>00:00:00</div></div><div class='intensidad3' style='width:"+wHijo+"%'><div class='area'><div style='height: 100%; display: inline-block; width: 0px;'></div><div class='barra'></div></div><div class='tag'>00:00:00</div></div><div class='intensidad2' style='width:"+wHijo+"%'><div class='area'><div style='height: 100%; display: inline-block; width: 0px;'></div><div class='barra'></div></div><div class='tag'>00:00:00</div></div><div class='intensidad4' style='width:"+wHijo+"%'><div class='area'><div style='height: 100%; display: inline-block; width: 0px;'></div><div class='barra'></div></div><div class='tag'>00:00:00</div></div><div class='intensidad5' style='width:"+wHijo+"%'><div class='area'><div style='height: 100%; display: inline-block; width: 0px;'></div><div class='barra'></div></div><div class='tag'>00:00:00</div></div><div class='intensidad1' style='width:"+wHijo+"%'><div class='area'><div style='height: 100%; display: inline-block; width: 0px;'></div><div class='barra'></div></div><div class='tag'>00:00:00</div></div></div></div>";
	
	return resultado;
}

var PLANES_USUARIO={"inf":[]};

function addPrograma2Calendar(idEspecialidad,idPrograma,diasSemana,fechaInicio)
{
	var myJSONObject = "";
	/*
		Verificar datos necesarios para la ejecución de la función
	*/
	if(idEspecialidad == "" || typeof idEspecialidad == typeof undefined || idPrograma == "" || typeof idPrograma == typeof undefined || fechaInicio == "" || typeof fechaInicio == typeof undefined)
	{
		return;	
	}
	
	/*
		Se trata de obtener el objeto local con la información de los planes
	*/	
	try
	{
		if(myJSONObject == "" || typeof myJSONObject == typeof undefined)
		{
			var atr = localStorage.getItem("Data");//Obtener el objeto "BD" de Local Storage
			var htmlTxt = "";
			if(atr== "" || atr == null || typeof atr == 'undefined')//Si nunca se ha creado, se crea con valores nulos
			{
				myJSONObject ={"Data": []};
			}else
			{
				myJSONObject = JSON.parse(""+atr);	
			};	
		}		 
	}catch(e){}	
	/*
		Fin obtención objeto local
	*/
	var programa = myJSONObject['data'][parseInt(idEspecialidad)][parseInt(idPrograma)];//Se trae el objeto local desde idPrograma	
	var fechaSeparada = fechaInicio.split("-");//Fecha separada por - para obtener año,mes y dia por separado
	var fechaActual = new Date(fechaSeparada[0],(parseInt(fechaSeparada[1])-1),fechaSeparada[2]);
	var dias = diasSemana.split(",");
	
	$.each(programa['planes'],function(i,plan){
		$.each(plan['rutinas'],function(j,rutina){			
			
			while($.inArray(""+fechaActual.getDay(),dias) < 0)
			{				
				fechaActual.setDate(fechaActual.getDate()+1);
			}
			
			try
			{
				  if(!PLANES_USUARIO['inf'][fechaActual.getFullYear()])
				{
					PLANES_USUARIO['inf'][fechaActual.getFullYear()] = {};	
				}
				
				if(!PLANES_USUARIO['inf'][fechaActual.getFullYear()][fechaActual.getMonth()])
				{
					PLANES_USUARIO['inf'][fechaActual.getFullYear()][fechaActual.getMonth()]={};	
				}
			}catch(e){console.log("Error: "+e);}
			
			PLANES_USUARIO['inf'][fechaActual.getFullYear()][fechaActual.getMonth()][fechaActual.getDate()]={idE:idEspecialidad,idP:idPrograma,idPlan:plan['idPlan'],idRutina:rutina['idRutina']};
			fechaActual.setDate(fechaActual.getDate()+1);	
			
		});
	});
	console.log(PLANES_USUARIO);	
	window.localStorage.setItem("calendario",JSON.stringify(PLANES_USUARIO) );
}


$(document).ready(function(e) {
	date();  
	addPrograma2Calendar(1,1,"1,2,3,5","2014-09-23");
	//getEntrenos();
	//getUserId();
});