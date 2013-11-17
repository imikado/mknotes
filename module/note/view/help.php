<style>
h1{
border-bottom:1px solid gray;	
}
.rendu{
	margin:10px;
	border:1px dotted gray;
	padding:10px;
}
h2{
	background:#ddd;
	margin-left:10px;
}
</style>

<h1>Projets, taches et sous taches</h1>
	<h2>Projet</h2>
		<p>usage: ==mon projet</p>
		
		<div class="rendu">rendu:
		<p style="font-weight:bold;border-bottom:1px solid black;margin-top:20px">mon projet</p>
		</div>

	<h2>Tache</h2>
		<p>usage: -ma tache</p>
		
		<div class="rendu">rendu:
		<p style="border:1px solid gray;margin-left:15px;background:#b3d1dc;margin-top:4px;background:#b3d1dc">ma tache</p>
		</div>
		
	<h2>Sous tache</h2>
		<p>usage: --ma sous tache</p>
		
		<div class="rendu">rendu:
		<p style="border:1px solid gray;margin-left:15px;background:#b3d1dc;margin-top:4px;background:#b3d1dc">ma tache</p>
		<p style="border:1px solid gray;margin-left:30px;background:#eee;margin-top:4px;">ma sous tache</p>
		</div>
		
	<h2>Sous sous tache</h2>
		<p>usage: ---ma sous sous tache</p>
		
		<div class="rendu">rendu:
		<p style="border:1px solid gray;margin-left:15px;background:#b3d1dc;margin-top:4px;background:#b3d1dc">ma tache</p>
		<p style="border:1px solid gray;margin-left:30px;background:#eee;margin-top:4px;">ma sous tache</p>
		<p style="border:1px solid gray;margin-left:45px;background:#eee;margin-top:4px;">ma sous tache</p>
		</div>

<h1>Progression</h1>
	<h2>OK, tache finie</h2>
	<p>usage: OK</p>
		
		<div class="rendu">rendu:
		<p style="border:1px solid gray;margin-left:15px;background:#66c673;margin-top:4px;">ma tache <span style="font-weight:bold;color:white">OK</span></p>
		</div>
		
	<h2>Tache en cours</h2>
	<p>usage: RUN</p>
		
		<div class="rendu">rendu:
		<p style="border:1px solid gray;margin-left:15px;background:orange;margin-top:4px;">ma tache <span style="font-weight:bold;color:white">RUN</span></p>
		</div>
	
	

<h1>Ids</h1>
	<h2>Hashtag</h2>
		Vous pouvez cr&eacute;er des hashtags, il permettent:
		<ul>
			<li>d'avoir un identifiant de tache pour lier des taches entre elles</li>
			<li>d'avoir un identifiant de projet pour fusionner en affichage "admin" les taches de plusieurs personnes sur le meme projet</li>
		</ul>

		<p>usage: #monHashtag</p>

		<div class="rendu">rendu: <span style="color:darkred"> #monHashtag </span> </div>



<h1>Dates</h1>

	<h2>Date Start to Date End</h2>
		<p>usage: [dd/mm/YYYY-dd/mm/YYYY]</p>

		<p>exple: [24/12/2013-25/12/2013]</p>

		<div class="rendu">rendu: <span style="color:#4a909a;font-weight:bold"> [24/12/2013 au 25/12/2013] </span> </div>

	<h2>Date Start + charge</h2>
		<p>usage: [dd/mm/YYYY;N]</p>

		<p>exple: [24/12/2013;3]</p>

		<div class="rendu">rendu: <span style="color:#4a909a;font-weight:bold"> [24/12/2013 &nbsp; 3 jour] </span> </div>


	<h2>Date Start + charge + affectation</h2>
		<p>usage: [dd/mm/YYYY;N;N%]</p>

		<p>exple: [24/12/2013;3;100%]</p>

		<div class="rendu">rendu: <span style="color:#4a909a;font-weight:bold"> [24/12/2013 &nbsp; 3 jour &nbsp; 100%] </span> </div>


	<h2>tache li&eacute;e + charge + affectation</h2>
		<p>usage: [hashtag;N;N%]</p>

		<p>exple: [maTacheParente;3;100%]</p>

		<div class="rendu">rendu: [ <span style="color:darkred"> #hashtag </span><span style="color:#4a909a;font-weight:bold">&nbsp; 3 jour &nbsp; 100%] </span> </div>

	<h2>Jalons</h2>
		<p>Vous pouvez ajoutez une tache "jalon", et y lier une ou plusieurs taches: si l'une d'elle depasse celle-ci, sa frise passe en rouge au del&agrave; du jalon</p>

		<p>usage: !hashtag</p>
		
		<p>exple: !maTacheJalon</p>
