
function SendForm(Url, FormId, Method)
	{
	var ArrayTinyMCE=$$('#'+FormId+' textarea.mceEditor'); 
	for(i=0; i<ArrayTinyMCE.length; i++){tinyMCE.execCommand('mceRemoveControl', false, FormId+ArrayTinyMCE[i].name);}

	var pars = $(FormId).serialize();
	var form=$(FormId); 
	form.disable();
	//alert(pars);
	var myAjax = new Ajax.Request(Url, {method: Method, parameters: pars, onComplete: showResponse});
	}

function showResponse(originalRequest)
	{
	alert(originalRequest.responseText); 
	//prompt ("!", originalRequest.responseText);
	
	var Answer=originalRequest.responseText.evalJSON();

	var Form = $(Answer.FormId); 
	
	Form.enable();
	alert(Answer.Answer);
	

	if(Answer.FlagCorrectInsert=="true")
		{
		if(Answer.ParentSBs!="")
			{
			ParentForms=Answer.ParentForms.split("|");
			ParentSBs=Answer.ParentSBs.split("|");
			SourceFieldValues=Answer.SourceFieldValues.split("|");
			//alert(SourceFieldValues[i]);
			
			//необходимо добавить проверку элемента в родительском окне.
			for(i=0; i<(ParentSBs.length-1); i++)
				{
				AddToSB(window.opener.document.forms(ParentForms[i]).elements(ParentSBs[i]), SourceFieldValues[i], Answer.InsertId, "#BFDEE6");
				}
			window.close();
			}
		
		//alert(Answer.NextAction);
		if((Answer.NextAction=='')||(Answer.NextAction=="0"))
			{
			
			if($F(Form.elements['NextAction'])!="0")
				{
				
				if($F(Form.elements['NextAction'])=="x")
					{
					window.close();
					}
				else
					{
					location.href='http://'+location.host+location.pathname+'?menu_marker='+$F(Form.elements['NextAction'])+'&'+Answer.PrimaryKey+'='+Answer.InsertId;
					//alert('http://'+location.host+location.pathname+'?menu_marker='+$F(Form.elements['NextAction'])+'&'+Answer.PrimaryKey+'='+Answer.InsertId);
					}						
				}
			else
				{
				if(Answer.FlagNotFormReset!="true")
					{Form.reset();}}
			}
		else
			{
			location.href='http://'+location.host+location.pathname+'?menu_marker='+Answer.NextAction+'&'+Answer.PrimaryKey+'='+Answer.InsertId;
			}

		}
		
	var ArrayTinyMCE=$$('#'+Answer.FormId+' textarea.mceEditor'); 
	for(i=0; i<ArrayTinyMCE.length; i++){tinyMCE.execCommand('mceAddControl', false, Answer.FormId+ArrayTinyMCE[i].name);}
	
	//alert(Answer.Query);
	//alert(originalRequest.responseText);
	}


function CheckboxInversion(ElementsName, FormsName)
	{
	var FormLength=document.forms(FormsName).length;
	for(i=0; i<FormLength; i++)
		{
		CheckBoxName=document.forms(FormsName).elements(i).name;
		j=CheckBoxName.search(ElementsName);
		//alert(ElementsName+"  "+CheckBoxName);
		if((j!=-1)||(ElementsName==CheckBoxName))
			{
			if(document.forms(FormsName).elements(i).type=='checkbox')
				{
				if(document.forms(FormsName).elements(i).checked)
				document.forms(FormsName).elements(i).checked=false;
				else
				document.forms(FormsName).elements(i).checked=true;
				}
			}
		}

	}

function Submit(FormId, CheckboxName)
	{
	var form = $(FormId); 

	var ArrCheckbox=form.getInputs('checkbox');
	var TrueCount=0;

	for(i=0; i<ArrCheckbox.length; i++)
		{
		j=ArrCheckbox[i].name.search(CheckboxName);
		if(j==0)
			{
			if(ArrCheckbox[i].checked)
				{TrueCount=TrueCount+1;}
			}
		}

	if(TrueCount>0)
		{
		if(confirm("Подтвердить?"))
			{form.submit();}
		}
	else{alert('Отсутствуют элементы.');}
	}

function OpenWindow(url, width, height)
	{
	height=height+30;
	width=width+40;
	width=(width>window.screen.width)?(window.screen.width-10):width;
	height=(height>window.screen.height)?(window.screen.height-70):height;

	var Left=(window.screen.width-width)/2;
	var Top=(window.screen.height-height)/2;

	var Win = window.open(url, "", "height="+height+", "+"width="+width+", toolbar=0, location=0, directories=0, status=1, menubar=0, scrollbars=1, left="+Left+", top="+Top+", resizable=1");
    //Win.focus();
	}

function ChangeTextFieldValue(Name, Value)
	{
	$(Name).value=Value;
	}

function OpenInvisibleElement(Id, ParentId)
	{
	$(Id).style.display='block';
	$(ParentId).style.display='none';
	}
	

function ChangeDisplay(Id)
	{
	
	if($(Id).style.display=='block')
		{$(Id).style.display='none';}
	else
		{$(Id).style.display='block';}
		
	//alert($(Id).style.display)
	}

function SearchForSB(TextField, SelectBoxField, FormName)
	{
	//alert(TextField);
	var TextFieldValue;
	TextFieldValue=document.forms(FormName).elements(TextField).value;
	TextFieldValue=TextFieldValue.toLowerCase();


	for(i=0; i<document.forms(FormName).elements(SelectBoxField).length; i++)
		{
		stroka=	document.forms(FormName).elements(SelectBoxField).options[i].text;
		stroka=stroka.toLowerCase();
		j=stroka.search(TextFieldValue);
		if(j!=-1)
			{
			document.forms(FormName).elements(SelectBoxField).options[i].selected=true;
			break;
			}
		}
	}

function SearchByAJAX(Url, ID, MinLength, ConID, Name, PKField, PKValue) 
	{
	var SubString = $F(ID);
	//alert(Url);
	
	if(SubString.length>=MinLength)
		{
		$(ConID).style.display="block";
		//$('searching').show();
	 
		new Ajax.Updater(ConID, Url, 
			{ method: 'post', parameters: { SubStringForAJAXSeach: SubString, FieldForAJAXSeach: Name}, onComplete: function () 
				{
				//$(ConID).show();
				//$('searching').hide();
				}
			}
						);

		}
	else
		{$(ConID).style.display="none";}
	}
	
//Добавление позиции в SelectBox
//------------------------------------------------------------------------------------------------------------------
function AddToSB(SBObject, SBText, SBId, Color)
	{
	var SelectArray=SBObject.options;
	var SelectArrayLength=SelectArray.length;
	var OptionElem=SBObject.form.document.createElement("OPTION");
	OptionElem.text=SBText;
	OptionElem.value=SBId;
	OptionElem.selected=true;
	OptionElem.style.background=Color;
	SelectArray.add(OptionElem);
	}
//------------------------------------------------------------------------------------------------------------------

function PasswordCompare(FPassField, SPassField, FormName)
	{
	//alert(TextField);
	
	if(document.forms(FormName).elements(FPassField).value==document.forms(FormName).elements(SPassField).value)
		{
		document.forms(FormName).elements(FPassField).style.color="#339900";
		document.forms(FormName).elements(SPassField).style.color="#339900";

		}
	else
		{
		document.forms(FormName).elements(FPassField).style.color="#CC0000";
		document.forms(FormName).elements(SPassField).style.color="#CC0000";
		}

	}
	
function get_trans(ag_val) 
	{    
	en_to_ru = {
		"А": "A","Б": "B","В": "V","Г": "G",
        "Д": "D","Е": "E", "Ё": "E", "Ж": "ZH","З": "Z","И": "I",
        "Й": "Y","К": "K","Л": "L","М": "M","Н": "N",
        "О": "O","П": "P","Р": "R","С": "S","Т": "T",
        "У": "U","Ф": "F","Х": "H","Ц": "TS","Ч": "CH",
        "Ш": "SH","Щ": "SHCH","Ъ": "","Ы": "Y","Ь": "",
        "Э": "E","Ю": "YU","Я": "YA","ЬЕ": "YE", "ЫЙ": "Y", "а": "a","б": "b",
        "в": "v","г": "g","д": "d","е": "e","ё": "e","ж": "zh",
        "з": "z","и": "i","й": "y","к": "k","л": "l",
        "м": "m","н": "n","о": "o","п": "p","р": "r",
        "с": "s","т": "t","у": "u","ф": "f","х": "h",
        "ц": "ts","ч": "ch","ш": "sh","щ": "shch","ъ": "",
        "ы": "y","ь": "","э": "e","ю": "yu","я": "ya", "ье": "ye", "ый": "y", " ":"_"};
    ag_val = ag_val.split(""); 
	trans = new String(); 
	for (i = 0; i < ag_val.length; i++) // перебираем все буквы 
		{
		for (key in en_to_ru) // перебираем все get_trans 
			{        
			val = en_to_ru[key]; // одно знчение       
			if (key == ag_val[i]) // если наша буква находится в массиве значений     
				{ 
				trans += val; // возвращаем нашу букву              
				break;         
				}
			else if (key == "m") 
				{          
				trans += ag_val[i]    
				}    
			}   
		}   
		return trans;
	}	