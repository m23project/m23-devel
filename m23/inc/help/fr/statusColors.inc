<!--V1.5 changed-->Lorsque vous cliquez sur le nom d'un client, s'afficheront des informations d�taill�es sur le client s�lectionn�; de plus, vous aurez acc�s au centre de contr�le.

<u>Signification des couleurs symboliques</u>
Le couleur du symbol d'�tat indique l'�tat de l'installation du poste client.
<ul>
<li><b>rouge:</b> Le poste client a �t� ajout� et la d�tection du mat�riel automatique n'est pas encore termin�e.</li>
<li><b>jaune:</b> Vous pouvez formater et partitionner le poste client, le syst�me de base sera assign� automatiquement.</li>
<li><b>vert:</b> Le syst�me de base du poste client est �tabli et vous pouvez installer les logiciels suppl�mentaires.</li>
<li><b>bleu:</b> Le poste client est en train d'installer des logiciels suppl�mentaires.</li>
<!--V1.5 begin--><li><b>orange:</b> Le poste client est dans un <b>�tat critique</b>. Cela veut dire qu'une erreur s'est produite pendant l'installation; elle doit �tre inhib�e par l'administrateur. Dans le centre de contr�le, plusieures possibilit�s d'inhiber l'�tat critique sont offertes.</li>
<li><b>blanc:</b> Poste client mod�le pour l'installation de masse dont les configurations seront transmises aux autres postes client.</li>
<li><b>Mouche (bogue):</b> Indique que le poste client se trouve en �tat de d�bogage.</li><!--V1.5 end-->
</ul>

<!--V1.11 begin--><u>$I18N_jobs</u>
Dans cette colonne, s'affiche le nombre des t�ches en attente avant la barre oblique et le nombre total des t�ches apr�s celle-ci.<!--V1.11 end-->

<!--V1.5 begin--><u>Travailler plusieurs postes clients</u>
Vous pouvez choisir plusieurs postes client en mettant une coche dans la colonne des postes clients souhait�s.

Puis, vous pouvez exercer des actions diff�rentes sur les postes client choisis:
<ul>
	<LI><b>$I18N_removeFromGroup:</b> S�lectionnez le groupe duquel vous voulez effacer les postes client. Si un poste client n'appartient pas � ce groupe, aucune action sera excerc� sur lui.</LI>
	<LI><b>$I18N_addToGroup:</b> S�lectionnez le groupe auquel vous souhaitez ajouter les postes client. Si un poste client appartient d�j� � ce groupe, sa d�pendance restera inchang�e.</LI>
	<!--V12.3 begin-->
	<LI><b>$I18N_moveToGroup</b>: Cette fonction d�place un poste client dans un groupe diff�rent.</LI>
	<LI><b>$I18N_recover</b>: R�installation compl�te du poste client.</LI>
	<!--V12.3 end-->
	<LI><b>$I18N_delete:</b> Efface les postes client s�lectionn�s</LI>
</ul><!--V1.5 end-->

<!--V1.2 begin--><u>Notez</u>
Pour voir l'�tat actuel de vos postes client, utilisez la fonction de rechargement de votre navigateur web (par exemple en appuyant sur la touche F5)<!--V1.2 end-->

<!--V1.5 begin--><u>Astuces</u>
En cliquant sur le symbole d'�tat d'un poste client, vous actualisez son �tat. Attention, vous devriez seulement changer l'�tat d'un poste client si vous savez exactement ce que vous faites.
Le mode de d�bogage peut �tre (d�s)activ� par un clic sur l'ic�ne du bogue.
<!--V1.5 end-->
