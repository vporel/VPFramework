@echo off


	set /P files= Noms des fichiers - sans extension - : 
	for %%f in (%files%) do start sass %%f.scss ../%%f.css -w

