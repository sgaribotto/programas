<div id="Botonera">
	
	<img src="./images/logo_eeyn.png" class="NavBar" Alt="EEYN - UNSAM"/>
	<div class="NavBar">
	<?php //print_r($_SERVER); ?>
	<?php if ( isset($_SESSION['usuario']) ) {
		?>
			
			
			<?php
				
				if (isset($_SESSION['permiso']) and in_array(1, $_SESSION['permiso'])) {
					
					?>
			<div class="menuItem">
			<a class="navBar toggleMenu" >Cargar o editar</a>
					
					<ul class="menuAdministrador" style="display:none;">
						<li><a class="menuAdministrador" href="docentes.php">Docentes</a></li>
						<li><a class="menuAdministrador" href="materias.php">Materias</a></li>
						<li><a class="menuAdministrador" href="responsables.php">Responsables</a></li>
						<li><a class="menuAdministrador" href="carreras.php">Carreras</a></li>
						<li><a class="menuAdministrador" href="personal.php">Personal</a></li>
					</ul>
			</div>
			<div class="menuItem">
			<a class="navBar toggleMenu" >Gestión Aulas</a>
				<ul class="menuAdministrador" style="display:none;">
					<li><a class="menuAdministrador" href="turnos.php">Turnos</a></li>
					<li><a class="menuAdministrador" href="inscriptos.php">Inscriptos</a></li>
					<li><a class="menuAdministrador" href="aulas.php">Aulas</a></li>
					<li><a class="menuAdministrador" href="aulificator2015.php">Asignar aulas</a></li>
					<li><a class="menuAdministrador" href="aulificatorreport.php">Reporte</a></li>
					<li><a class="menuAdministrador" href="actualizarbaseinscriptos.php">Actualizar los inscriptos</a></li>
					
				</ul>
			</div>
					
			
				<?php } else if (isset($_SESSION['permiso']) 
						and (in_array(4, $_SESSION['permiso']) or in_array(5, $_SESSION['permiso']) or in_array(6, $_SESSION['permiso']))  ){?>
					
					
				<?php } ?>
				<a href="cerrarsesion.php" class="navBar">Cerrar Sesión (
					<?php echo $_SESSION['usuario']; ?>
				)</a>
				<a href="cambiarclave.php" class="navBar">Cambiar Clave</a>
			<?php }?>
		</div>
	
</div>

<style>
	#Botonera
		{
		width:99%;
		position:fixed;
		top:2px;
		color:black;
		font-size:1.1em;
		text-align:left;
		margin:2px auto;
		background-color:white;
		box-shadow:0 5px 5px 0 gray;
		z-index:1000;
		}
	
	a.navBar
		{
		text-decoration:none;
		display:inline;
		color:black;
		padding:5px;
		height:40px;
		margin:5px;
		cursor:pointer;
		border-radius:5px;
		}
	
	a.menuAdministrador {
		text-decoration:none;
		font-size:.8em;
		
	}
	
	a.navBar:Hover
		{
		background-color:#D6EBFF;
		}
	
	img.NavBar
		{
		padding:4px;
		height:28px;
		float:right;
		border-top:solid 2px black;
		border-bottom:solid 2px black;
		}
	
	div.NavBar
		{
		text-align:left;
		width:95%;
		height:16px;
		margin-left:2px;
		padding:10px;
		border-top:solid 2px black;
		border-bottom:solid 2px black;
		}
	
	ul.menuAdministrador {
		position:absolute;
	}
	
			li.elementosMenu {
			
			
		}
		
	 .ui-menu { 
		width: 150px; 
		
	}
	
	div.menuItem {
		display:inline-block;
	}
		
</style>

<style media="print">
	#Botonera {display:none;}
</style>
	

	<script>
		$(document).ready(function() {
			
			
			$("ul.menuAdministrador").menu({
				
			});
			
			$("div.dialog").dialog({
				autoOpen: false,
				width: 800,
			});
			
			$('a.toggleMenu').click( function(event){
				event.stopPropagation();
				$(this).next('ul.menuAdministrador').slideToggle();
			});
			
			$(document).click( function(){
				$('ul.menuAdministrador').fadeOut();
			});
			
			$('ul.menuAdministrador li').click(function() {
				location.assign($(this).children('a').attr('href'));
			});
			
			$('table.plantelActual').click(function(event) {
				if ($(event.target).is('td:first-child()')){
					$clicked = $(event.target);
					materia = $clicked.text().replace("(", '').replace(")", '');
					materia = materia.split(', ');
					url = "resumenmateria.php?materia=" + materia[0];
					$('div.dialog.resumenMateria')
						.load(url)
						.dialog('open');
				}
			});
			
			
			
		});
		
		
	</script>
