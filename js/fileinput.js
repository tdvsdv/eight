
var numFiles=new Array();
var numFilesInDir=new Array();
var inputName=new Array();
var inputMaxNumber=new Array();
//window.onload = function(){ AddInput() };
var IDC=1;

function HandleChanges(id, ContainerID)
	{
	file = document.getElementById(id).value;
	 
	    
	reWin = /.*\\(.*)/;
	var fileTitle = file.replace(reWin, "$1"); //выдираем название файла для w*s
	reUnix = /.*\/(.*)/;
	fileTitle = fileTitle.replace(reUnix, "$1"); //выдираем название для *nix
	    
	fileName = document.getElementById('name'+id);
	fileName.innerHTML = fileTitle;
	var RegExExt =/.*\.(.*)/;
	var ext = fileTitle.replace(RegExExt, "$1");//и его расширение
	    
	var pos;
	if (ext)
		{
		switch (ext.toLowerCase())
			{
		    case 'doc': pos = '0'; break;
		    case 'bmp': pos = '16'; break;       
		    case 'jpg': pos = '32'; break;
		    case 'jpeg': pos = '32'; break;
		    case 'png': pos = '48'; break;
		    case 'gif': pos = '64'; break;
		    case 'psd': pos = '80'; break;
		    case 'mp3': pos = '96'; break;
		    case 'wav': pos = '96'; break;
		    case 'ogg': pos = '96'; break;
		    case 'avi': pos = '112'; break;
		    case 'wmv': pos = '112'; break;
		    case 'flv': pos = '112'; break;
		    case 'pdf': pos = '128'; break;
		    case 'exe': pos = '144'; break;
		    case 'txt': pos = '160'; break;
		    default: pos = '176'; break;
			};
			
		fileName.style.display = 'block';
		fileName.style.background = 'url(./images/fileinput/icons.png) no-repeat 0 -'+pos+'px';
		};
	
	
	IDC1=IDC-1;
	UplButton=document.getElementById('bb'+'text'+IDC1);
	UplButton.style.display="none";
	
	DelButton=document.getElementById('db'+'text'+IDC1);
	DelButton.style.display="block";
	
	FileInput=document.getElementById('text'+IDC1);
	FileInput.style.display="none";
	
	AddInput(ContainerID);
	
	
	};

function AddInput(ContainerID)
	{  
	var IA=new Array();
	
	container=document.getElementById(ContainerID);
	
	if(inputMaxNumber[ContainerID]>numFiles[ContainerID])
		{
	    wraper = document.createElement('div');
	    wraper.className = 'wrapper';
		//wraper.innerHTML='<div class=fi_number>'+(numFiles[ContainerID]+1)+'.</div>';
	    fileInput = document.createElement('input');
	    fileInput.value = '';
	    fileInput.setAttribute('type','file');
	    var id = 'text'+IDC;
	    wraper.setAttribute('id','wrapper'+id);
	    fileInput.setAttribute('id',id);
		//fileInput.setAttribute('name', inputName[ContainerID]+numFiles[ContainerID]);
	    fileInput.className = 'customFile';
	    fileInput.onchange = function(){ HandleChanges(id, ContainerID) };
	    fileInput.onmouseover = function() { MakeActive(id) };
	    fileInput.onmouseout = function() { UnMakeActive(id) };
	    fileName = document.createElement('div');
	    fileName.style.display = 'none';
	    fileName.style.background = 'url(./images/fileinput/icons.png)';
	    fileName.setAttribute('id','name'+id);
	    fileName.className = "FileName";
	    bb = document.createElement('div');
	    bb.setAttribute('id','bb' + id);
	    bb.className = 'fakeButton';
		bb.innerHTML='<a href=\'\'>Найти файл</a>';
	    bl = document.createElement('div');
	    bl.setAttribute('id','bl' + id);
	    bl.className = 'blocker';
	    deleteButton = document.createElement('div');
		deleteButton.setAttribute('id','db' + id);
		deleteButton.setAttribute('title', 'Убрать из списка');
	    deleteButton.className = 'minus';
	    deleteButton.onclick = function() { DeleteCustomInput(id, ContainerID) };	
		
	
	    wraper.appendChild(bb);
	    wraper.appendChild(bl);
	    wraper.appendChild(fileInput);
		wraper.appendChild(deleteButton);
	    wraper.appendChild(fileName);
	    container.appendChild(wraper);
	    numFiles[ContainerID]++;
		}
	/*else
		{
		Plus=document.getElementById('FIPlus_'+ContainerID);
		Plus.style.display='none';
		}*/
		

	inputs=$$('#'+ContainerID+' div.wrapper input.customFile'); 
	//inputs = getElementsByClassName('customFile');
	for (var i = 0, j=numFilesInDir[ContainerID]; i < inputs.length; i++, j++)
		{
		inputs[i].setAttribute('name', inputName[ContainerID]+j);
		//alert(inputs[i].name);
		}
		    
	IDC++;
	};

function DeleteCustomInput(id, ContainerID)
	{
	    i = document.getElementById('wrapper'+id);
	    i.parentNode.removeChild(i);
		numFiles[ContainerID]--;
	}

function MakeActive(id)
	{
	/*bb = document.getElementById('bb'+id);
	bb.style.backgroundPosition = '0 -21px';*/
	bb.innerHTML='<a href=\'\' style=\'color:#CC0000;\'>Найти файл</a>';
	};
function UnMakeActive(id)
	{
	/*bb = document.getElementById('bb'+id);
	bb.style.backgroundPosition = '0 0';*/
	bb.innerHTML='<a href=\'\'>Найти файл</a>';
	};

/*
function getElementsByClassName(searchClass) {
	var classElements = new Array();
	var els = document.getElementsByTagName('*');
	var elsLen = els.length;
	var pattern = new RegExp("(^|\\\\s)"+searchClass+"(\\\\s|$)");
	for (i = 0, j = 0; i < elsLen; i++) {
		if ( pattern.test(els[i].className) ) {
			classElements[j] = els[i];
			j++;
		}
	}
	return classElements;
};*/
