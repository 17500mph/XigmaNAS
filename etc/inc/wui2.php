<?php
/*
	wui2.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2017 The NAS4Free Project <info@nas4free.org>.
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice, this
	   list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright notice,
	   this list of conditions and the following disclaimer in the documentation
	   and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
	ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
	ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

	The views and conclusions contained in the software and documentation are those
	of the authors and should not be interpreted as representing official policies,
	either expressed or implied, of the NAS4Free Project.
*/
require_once 'config.inc';
require_once 'array.inc';

class HTMLBaseControl2 {
	var $_ctrlname = '';
	var $_title = '';
	var $_description = '';
	var $_value;
	var $_required = false;
	var $_readonly = false;
	var $_altpadding = false;
	var $_classtag = 'celltag';
	var $_classdata = 'celldata';
	var $_classaddonrequired = 'req';
	var $_classaddonpadalt = 'alt';
	//	constructor
	public function __construct($ctrlname,$title,$value,$description = '') {
		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetDescription($description);
		$this->SetValue($value);
	}
	//	get/set methods
	function SetAltPadding($bool) {
		$this->_altpadding = $bool;
	}
	function GetAltPadding() {
		return $this->_altpadding;
	}
	function SetClassAddonRequired($cssclass) {
		$this->_classaddonrequired = $cssclass;
	}
	function GetClassAddonRequired() {
		return $this->_classaddonrequired;
	}
	function SetClassAddonPadAlt($cssclass) {
		$this->_classaddonpadalt = $cssclass;
	}
	function GetClassAddonPadAlt() {
		return $this->_classaddonpadalt;
	}
	function SetClassData($cssclass) {
		$this->_classdata = $cssclass;
	}
	function GetClassData() {
		return $this->_classdata;
	}
	function SetClassTag($cssclass) {
		$this ->_classtag = $cssclass;
	}
	function GetClassTag() {
		return $this->_classtag;
	}
	function SetCtrlName($name) {
		$this->_ctrlname = $name;
	}
	function GetCtrlName() {
		return $this->_ctrlname;
	}
	function SetDescription($description) {
		$this->_description = $description;
	}
	function GetDescription() {
		return $this->_description;
	}
	function SetReadOnly($bool) {
		$this->_readonly = $bool;
	}
	function GetReadOnly() {
		return $this->_readonly;
	}
	function SetRequired($bool) {
		$this->_required = $bool;
	}
	function GetRequired() {
		return $this->_required;
	}
	function SetTitle($title) {
		$this->_title = $title;
	}
	function GetTitle() {
		return $this->_title;
	}
	function SetValue($value) {
		$this->_value = $value;
	}
	function GetValue() {
		return $this->_value;
	}
	//	support methods
	function GetClassOfTag() {
		$class = $this->GetClassTag();
		if(true === $this->GetRequired()):
			$class .= $this->GetClassAddonRequired();
		endif;
		if(true === $this->GetAltPadding()):
			$class .= $this->GetClassAddonPadAlt();
		endif;
		return $class;
	}
	function GetClassOfData() {
		$class = $this->GetClassData();
		if(true === $this->GetRequired()):
			$class .= $this->GetClassAddonRequired();
		endif;
		if(true === $this->GetAltPadding()):
			$class .= $this->GetClassAddonPadAlt();
		endif;
		return $class;
	}
	function GetDescriptionOutput() {
		//	description:
		//	string
		//	[string, ...]
		//	[ [string], ...]
		//	[ [string,no_br], ...]
		//	[ [string,color], ...]
		//	[ [string,color,no_br], ...]
		$description = $this->GetDescription();
		$description_output = '';
		$suppressbr = true;
		if(!empty($description)): // string or array
			if(is_string($description)):
				$description_output = $description;
			elseif(is_array($description)):
				foreach($description as $description_row):
					if(is_string($description_row)):
						if($suppressbr):
							$description_output .= $description_row;
							$suppressbr = false;
						else:
							$description_output .= ('<br />' . $description_row);
						endif;
					elseif(is_array($description_row)):
						switch(count($description_row)):
							case 1:
								if(is_string($description_row[0])):
									if($suppressbr):
										$suppressbr = false;
									else:
										$description_output .= '<br />';
									endif;
									$description_output .= $description_row[0];
								endif;
								break;
							case 2:
								if(is_string($description_row[0])):
									$color = NULL;
									if(is_string($description_row[1])):
										$color = $description_row[1];
									endif;
									if(is_bool($description_row[1])):
										$suppressbr = $description_row[1];
									endif;
									if($suppressbr):
										$suppressbr = false;
									else:
										$description_output .= '<br />';
									endif;
									if(is_null($color)):
										$description_output .= $description_row[0];
									else:
										$description_output .= sprintf('<span style="color:%2$s">%1$s</span>',$description_row[0],$color);
									endif;
								endif;
								break;
							case 3: // allow not to break
								if(is_string($description_row[0])):
									$color = NULL;
									if(is_string($description_row[1])):
										$color = $description_row[1];
									endif;									
									if(is_bool($description_row[2])):
										$suppressbr = $description_row[2];
									endif;
									if($suppressbr):
										$suppressbr = false;
									else:
										$description_output .= '<br />';
									endif;
									if(is_null($color)):
										$description_output .= $description_row[0];
									else:
										$description_output .= sprintf('<span style="color:%2$s">%1$s</span>',$description_row[0],$color);
									endif;
								endif;
								break;
						endswitch;
					endif;
				endforeach;
			endif;
		endif;
		return $description_output;
	}
	function Compose(DOMNode &$anchor = NULL) {
		//	create root DOM if anchor not provided
		if(is_null($anchor)):
			$anchor = new co_DOMDocument();
		endif;
		$description = $this->GetDescriptionOutput();
		//	compose
		$attributes = ['id' => sprintf('%s_tr',$this->GetCtrlName())];
		$tr = $anchor->addElement('tr',$attributes);
		$attributes = ['class' => $this->GetClassOfTag()];
//		if($this->GetReadOnly()):
			$tr->addElement('td',$attributes,$this->GetTitle());
//		else:
//			$tdtag = $tr->addElement('td',$attributes);
//			$attributes = ['for' => $ctrlname];
//			$tdtag->addElement('label',$attributes,$this->GetTitle());
//		endif;
		$attributes = ['class' => $this->GetClassOfData()];
		$tddata = $tr->addElement('td',$attributes);
		$this->ComposeInner($tddata);
		if(!empty($description)):
			$attributes = ['class' => 'formfldadditionalinfo'];
			$tddata->addElement('div',$attributes,$description);
		endif;
		return $anchor;
	}
	function ComposeInner(&$anchor) {
	}
}
class HTMLBaseControlJS2 extends HTMLBaseControl2 {
	var $_onclick = '';
	function SetJSonClick($code) {
		$this->_onclick = $code;
	}
	function GetJSonClick() {
		return $this->_onclick;
	}
}
class HTMLEditBox2 extends HTMLBaseControl2 {
	var $_size = 40;
	var $_maxlength = 0;
	var $_placeholder = '';
	var $_classinputtext = 'formfld';
	var $_classinputtextro = 'formfldro';

	//	constructor
	function __construct($ctrlname,$title,$value,$description,$size) {
		parent::__construct($ctrlname,$title,$value,$description);
		$this->SetSize($size);
	}
	//	get/set methods
	function SetClassInputText($param) {
		$this->_classinputtext = $param;
	}
	function GetClassInputText() {
		return $this->_classinputtext;
	}
	function SetClassInputTextRO($param) {
		$this->_classinputtextro = $param;
	}
	function GetClassInputTextRO() {
		return $this->_classinputtextro;
	}
	function SetMaxLength($maxlength) {
		$this->_maxlength = $maxlength;
	}
	function GetMaxLength() {
		return $this->_maxlength;
	}
	function SetPlaceholder(string $placeholder = '') {
		$this->_placeholder = $placeholder;
	}
	function GetPlaceholder() {
		return $this->_placeholder;
	}
	function SetSize($size) {
		$this->_size = $size;
	}
	function GetSize() {
		return $this->_size;
	}
	//	support methods
	function GetAttributes(array &$attributes = []) {
		if(true === $this->GetReadOnly()):
			$attributes['readonly'] = 'readonly';
		endif;
		$tagval = $this->GetPlaceholder();
		if(preg_match('/\S/',$tagval)):
			$attributes['placeholder'] = $tagval;
		endif;
		$tagval = $this->GetMaxLength();
		if($tagval > 0):
			$attributes['maxlength'] = $tagval;
		endif;
		return $attributes;
	}
	function GetClassOfInputText() {
		if(true === $this->GetReadOnly()):
			return $this->GetClassInputTextRO();
		else:
			return $this->GetClassInputText();
		endif;
	}
	function ComposeInner(&$anchor) {
		$attributes = [
			'type' => 'text',
			'id' => $this->GetCtrlName(),
			'name' => $this->GetCtrlName(),
			'class' => $this->GetClassOfInputText(),
			'size' => $this->GetSize(),
			'value' => $this->GetValue()
		];
		$this->GetAttributes($attributes);
		$anchor->addElement('input',$attributes);
	}
}
class HTMLPasswordBox2 extends HTMLEditBox2 {
	var $_classinputpassword = 'formfld';
	//	get/set methods
	function SetClassInputPassword($cssclass) {
		$this->_classinputpassword = $cssclass;
	}
	function GetClassInputPassword() {
		return $this->_classinputpassword;
	}
	//	support methods
	function GetClassOfInputPassword() {
		return $this->GetClassInputPassword();
	}
	function ComposeInner(&$anchor) {
		$ctrlname = $this->GetCtrlName();
		$attributes = [
			'type' => 'password',
			'id' => $ctrlname,
			'name' => $ctrlname,
			'class' => $this->GetClassOfInputPassword(),
			'size' => $this->GetSize(),
			'value'=> $this->GetValue()
		];
		$this->GetAttributes($attributes);
		$anchor->addElement('input',$attributes);
	}
}
class HTMLPasswordConfBox2 extends HTMLEditBox2 {
	var $_ctrlnameconf = '';
	var $_valueconf = '';
	var $_classinputpassword = 'formfld';
	var $_placeholderconfirm = '';
	//	constructor
	function __construct($ctrlname,$ctrlnameconf,$title,$value,$valueconf,$description,$size) {
		parent::__construct($ctrlname,$title,$value,$description,$size);
		$this->SetCtrlNameConf($ctrlnameconf);
		$this->SetValueConf($valueconf);
	}
	//	get/set methods
	function SetClassInputPassword($cssclass) {
		$this->_classinputpassword = $cssclass;
	}
	function GetClassInputPassword() {
		return $this->_classinputpassword;
	}
	function SetCtrlNameConf($name) {
		$this->_ctrlnameconf = $name;
	}
	function GetCtrlNameConf() {
		return $this->_ctrlnameconf;
	}
	function SetPlaceholderConfirm(string $placeholder = '') {
		$this->_placeholderconfirm = $placeholder;
	}
	function GetPlaceholderConfirm() {
		return $this->_placeholderconfirm;
	}
	function SetValueConf($value) {
		$this->_valueconf = $value;
	}
	function GetValueConf() {
		return $this->_valueconf;
	}
	//	support methods
	function GetAttributesConfirm(array &$attributes = []) {
		$attributes = $this->GetAttributes($attributes);
		$tagval = $this->GetPlaceholderConfirm();
		if(preg_match('/\S/',$tagval)):
			$attributes['placeholder'] = $tagval;
		endif;
		return $attributes;
	}
	function GetClassOfInputPassword() {
		return $this->GetClassInputPassword();
	}
	function ComposeInner(&$anchor) {
		$ctrlname = $this->GetCtrlName();
		$ctrlnameconf = $this->GetCtrlNameConf();
		$attributes = [
			'type' => 'password',
			'id' => $ctrlname,
			'name' => $ctrlname,
			'class' => $this->GetClassOfInputPassword(),
			'size' => $this->GetSize(),
			'value'=> $this->GetValue()
		];
		$this->GetAttributes($attributes);
		$o_div1 = $anchor->addElement('div');
		$o_div1->addElement('input',$attributes);
		$attributes = [
			'type' => 'password',
			'id' => $ctrlnameconf,
			'name' => $ctrlnameconf,
			'class' => $this->GetClassOfInputPassword(),
			'size' => $this->GetSize(),
			'value' => $this->GetValueConf()
		];
		$this->GetAttributesConfirm($attributes);
		$o_div2 = $anchor->addElement('div');
		$o_div2->addElement('input',$attributes);
	}
}
class HTMLTextArea2 extends HTMLEditBox2 {
	var $_columns = 40;
	var $_rows = 5;
	var $_wrap = true;
	var $_classtextarea = 'formpre';
	var $_classtextarearo = 'formprero';
	//	constructor
	function __construct($ctrlname,$title,$value,$description,$columns,$rows) {
		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetValue($value);
		$this->SetDescription($description);
		$this->SetColumns($columns);
		$this->SetRows($rows);
	}
	//	get/set methods
	function SetClasstextarea($cssclass) {
		$this->_classtextarea = $cssclass;
	}
	function GetClassTextarea() {
		return $this->_classtextarea;
	}
	function SetClasstextareaRO($cssclass) {
		$this->_classtextarearo = $cssclass;
	}
	function GetClassTextareaRO() {
		return $this->_classtextarearo;
	}
	function SetColumns($columns) {
		$this->_columns = $columns;
	}
	function GetColumns() {
		return $this->_columns;
	}
	function SetRows($rows) {
		$this->_rows = $rows;
	}
	function GetRows() {
		return $this->_rows;
	}
	function SetWrap($bool) {
		$this->_wrap = $bool;
	}
	function GetWrap() {
		return $this->_wrap;
	}
	//	support methods
	function GetAttributes(array &$attributes = []) {
		parent::GetAttributes($attributes);
		if(false === $this->GetWrap()):
			$attributes['wrap'] = 'soft';
		else:
			$attributes['wrap'] = 'hard';
		endif;
		return $attributes;
	}
	function GetClassOfTextarea() {
		return ($this->GetReadOnly() ? $this->GetClassTextareaRO() : $this->GetClassTextarea());
	}
	function ComposeInner(&$anchor) {
		$ctrlname = $this->GetCtrlName();
		$attributes = [
			'id' => $ctrlname,
			'name' => $ctrlname,
			'class' => $this->GetClassOfTextarea(),
			'cols' => $this->GetColumns(),
			'rows' => $this->GetRows()
		];
		$this->GetAttributes($attributes);
		$anchor->addElement('textarea',$attributes,htmlspecialchars($this->GetValue(),ENT_QUOTES));
	}
}
class HTMLFileChooser2 extends HTMLEditBox2 {
	var $_path = '';
	function __construct($ctrlname,$title,$value,$description,$size = 60) {
		parent::__construct($ctrlname,$title,$value,$description,$size);
	}
	function SetPath($path) {
		$this->_path = $path;
	}
	function GetPath() {
		return $this->_path;
	}
	function ComposeInner(&$anchor) {
		//	helper variables
		$ctrlname = $this->GetCtrlName();
		$size = $this->GetSize();
		//	input element
		$attributes = [
			'type' => 'text',
			'id' => $ctrlname,
			'name' => $ctrlname,
			'class' => 'formfld',
			'value' => $this->GetValue(),
			'size' => $size
		];
		$this->GetAttributes($attributes);
		$anchor->addElement('input',$attributes);
		//	file chooser
		$js = sprintf('%1$sifield = form.%1$s;',$ctrlname)
			. 'filechooser = window.open("filechooser.php?p="+'
			. sprintf('encodeURIComponent(%sifield.value)+',$ctrlname)
			. sprintf('"&sd=%s",',$this->GetPath())
			. '"filechooser",'
			. '"scrollbars=yes,toolbar=no,menubar=no,statusbar=no,width=550,height=300");'
			. sprintf('filechooser.ifield = %sifield;',$ctrlname)
			. sprintf('window.ifield = %sifield;',$ctrlname);
		$attributes = [
			'type' => 'button',
			'id' => $ctrlname . 'browsebtn',
			'name' => $ctrlname . 'browsebtn',
			'class' => 'formbtn',
			'value' => $value,
			'size' => $size,
			'onclick' => $js,
			'value' => '...'
		];
		$this->GetAttributes($attributes);
		$anchor->addElement('input',$attributes);
	}
}
class HTMLIPAddressBox2 extends HTMLEditBox2 {
	var $_ctrlnamenetmask = '';
	var $_valuenetmask = '';
	//	constructor
	function __construct($ctrlname,$ctrlnamenetmask,$title,$value,$valuenetmask,$description) {
		$this->SetCtrlName($ctrlname);
		$this->SetCtrlNameNetmask($ctrlnamenetmask);
		$this->SetTitle($title);
		$this->SetValue($value);
		$this->SetValueNetmask($valuenetmask);
		$this->SetDescription($description);
	}
	//	get/set Methods
	function SetCtrlNameNetmask($name) {
		$this->_ctrlnamenetmask = $name;
	}
	function GetCtrlNameNetmask() {
		return $this->_ctrlnamenetmask;
	}
	function SetValueNetmask($value) {
		$this->_valuenetmask = $value;
	}
	function GetValueNetmask() {
		return $this->_valuenetmask;
	}
}
class HTMLIPv4AddressBox2 extends HTMLIPAddressBox2 {
	//	constructor
	function __construct($ctrlname,$ctrlnamenetmask,$title,$value,$valuenetmask,$description) {
		parent::__construct($ctrlname,$ctrlnamenetmask,$title,$value,$valuenetmask,$description);
		$this->SetSize(20);
	}
	//	support methods
	function ComposeInner(&$anchor) {
		$ctrlname = $this->GetCtrlName();
		$ctrlnamenetmask = $this->GetCtrlNameNetmask();
		$valuenetmask = $this->GetValueNetmask();
		$attributes = [
			'type' => 'text',
			'id' => $ctrlname,
			'name' => $ctrlname,
			'class' => 'formfld',
			'value' => $this->GetValue(),
			'size' => $this->GetSize()
		];
		$anchor->addElement('input',$attributes);
		$slash = $anchor->ownerDocument->createTextNode(' / ');
		$anchor->appendChild($slash);
		$attributes = ['id' => $ctrlnamenetmask,'name' => $ctrlnamenetmask,'class' => 'formfld'];
		$o_select = $anchor->addElement('select',$attributes);
		foreach(range(1,32) as $netmask):
			$attributes = ['value' => $netmask];
			if($netmask == $valuenetmask):
				$attributes['selected'] = 'selected';
			endif;
			$o_select->addElement('option',$attributes,$netmask);
		endforeach;
	}
}
class HTMLIPv6AddressBox2 extends HTMLIPAddressBox2 {
	//	constructor
	function __construct($ctrlname,$ctrlnamenetmask,$title,$value,$valuenetmask,$description) {
		parent::__construct($ctrlname,$ctrlnamenetmask,$title,$value,$valuenetmask,$description);
		$this->SetSize(30);
	}
	//	support methods
	function ComposeInner(&$anchor) {
		$ctrlname = $this->GetCtrlName();
		$ctrlnamenetmask = $this->GetCtrlNameNetmask();
		$attributes = [
			'type' => 'text',
			'id' => $ctrlname,
			'name' => $ctrlname,
			'class' => 'formfld',
			'value' => $this->GetValue(),
			'size' => $this->GetSize()
		];
		$anchor->addElement('input',$attributes);
		$slash = $anchor->ownerDocument->createTextNode(' / ');
		$anchor->appendChild($slash);
		$attributes = [
			'type' => 'text',
			'id' => $ctrlnamenetmask,
			'name' => $ctrlnamenetmask,
			'class' => 'formfld',
			'value' => $this->GetValueNetmask(),
			'size' => 2
		];
		$anchor->addElement('input',$attributes);
	}
}
class HTMLCheckBox2 extends HTMLBaseControlJS2 {
	var $_caption = '';
	var $_classcheckbox = 'celldatacheckbox';
	var $_classcheckboxro = 'celldatacheckbox';
	//	constructor
	function __construct($ctrlname,$title,$value,$caption,$description = '') {
		parent::__construct($ctrlname,$title,$value,$description);
		$this->SetCaption($caption);
	}
	//	get/set methods
	function SetChecked($bool) {
		$this->SetValue($bool);
	}
	function IsChecked() {
		return $this->GetValue();
	}
	function SetCaption($caption) {
		$this->_caption = $caption;
	}
	function GetCaption() {
		return $this->_caption;
	}
	function SetClassCheckbox($cssclass) {
		$this->_classcheckbox = $cssclass;
	}
	function GetClassCheckbox() {
		return $this->_classcheckbox;
	}
	function SetClassCheckboxRO($cssclass) {
		$this->_classcheckboxro = $cssclass;
	}
	function GetClassCheckboxRO() {
		return $this->_classcheckboxro;
	}
	//	support methods
	function GetAttributes(array &$attributes = []) {
		if(true === $this->IsChecked()):
			$attributes['checked'] = 'checked';
		endif;
		if(true === $this->GetReadOnly()):
			$attributes['disabled'] = 'disabled';
		endif;
		$onclick = $this->GetJSonClick();
		if(!empty($onclick)):
			$attributes['onclick'] = $onclick;
		endif;
		return $attributes;
	}
	function GetClassOfCheckbox() {
		return ($this->GetReadOnly() ? $this->GetClassCheckboxRO() : $this->GetClassCheckbox());
	}
	function ComposeInner(&$anchor) {
		//	helper variables
		$ctrlname = $this->GetCtrlName();
		//	compose
		$div = $anchor->addElement('div',['class' => $this->GetClassOfCheckbox()]);
		$attributes = ['type' => 'checkbox','id' => $ctrlname,'name' => $ctrlname,'value' => 'yes'];
		$this->GetAttributes($attributes);
		$div->addElement('input',$attributes);
		$div->addElement('label',['for' => $ctrlname],$this->GetCaption());
	}
}
class HTMLSelectControl2 extends HTMLBaseControlJS2 {
	var $_ctrlclass = '';
	var $_options = [];
	//	constructor
	function __construct($ctrlclass,$ctrlname,$title,$value,$options,$description) {
		parent::__construct($ctrlname,$title,$value,$description);
		$this->SetCtrlClass($ctrlclass);
		$this->SetOptions($options);
	}
	//	get/set methods
	function SetCtrlClass($ctrlclass) {
		$this->_ctrlclass = $ctrlclass;
	}
	function GetCtrlClass() {
		return $this->_ctrlclass;
	}
	function SetOptions(array $options = []) {
		$this->_options = $options;
	}
	function GetOptions() {
		return $this->_options;
	}
	function GetAttributes(array &$attributes = []) {
		if(true === $this->GetReadOnly()):
			$attributes['disabled'] = 'disabled';
		endif;
		$onclick = $this->GetJSonClick();
		if(!empty($onclick)):
			$attributes['onclick'] = $onclick;
		endif;
		return $attributes;
	}
	function ComposeInner(&$anchor) {
		$ctrlname = $this->GetCtrlName();
		$value = $this->GetValue();
		$options = $this->GetOptions();
		$attributes = [
			'id' => $ctrlname,
			'name' => $ctrlname,
			'class' => $this->GetCtrlClass()
		];
		$this->GetAttributes($attributes);
		$select = $anchor->addElement('select',$attributes);
		foreach($options as $option_tag => $option_val):
			$attributes = ['value' => $option_tag];
			if($value == $option_tag):
				$attributes['selected'] = 'selected';
			endif;
			$select->addElement('option',$attributes,$option_val);
		endforeach;
	}
}
class HTMLMultiSelectControl2 extends HTMLSelectControl2 {
	var $_size = 10;
	//	constructor
	function __construct($ctrlclass,$ctrlname,$title,$value,$options,$description) {
		parent::__construct($ctrlclass,$ctrlname,$title,$value,$options,$description);
	}
	//	get/set methods
	function GetSize() {
		return $this->_size;
	}
	function SetSize($size) {
		$this->_size = $size;
	}
	//	support methods
	function ComposeInner(&$anchor) {
		$ctrlname = $this->GetCtrlName();
		$value = $this->GetValue();
		$options = $this->GetOptions();
		$attributes = [
			'id' => $ctrlname,
			'name' => sprintf('%s[]',$ctrlname),
			'class' => $this->GetCtrlClass(),
			'multiple' => 'multiple',
			'size' => $this->GetSize()
		];
		$this->GetAttributes($attributes);
		$select = $anchor->addElement('select',$attributes);
		foreach($options as $option_tag => $option_val):
			$attributes = ['value' => $option_tag];
			if(is_array($value) && in_array($option_tag,$value)):
				$attributes['selected'] = 'selected';
			endif;
			$select->addElement('option',$attributes,$option_val);
		endforeach;
	}
}
class HTMLComboBox2 extends HTMLSelectControl2 {
	//	constructor
	function __construct($ctrlname,$title,$value,$options,$description) {
		parent::__construct('formfld',$ctrlname,$title,$value,$options,$description);
	}
}
class HTMLRadioBox2 extends HTMLComboBox2 {
	function ComposeInner(&$anchor) {
		$ctrlname = $this->GetCtrlName();
		$value = $this->GetValue();
		$options = $this->GetOptions();
		$table = $anchor->addElement('table',['class' => 'area_data_selection']);
		$colgroup = $table->addElement('colgroup');
		$colgroup->addElement('col',['style' => 'width:5%']);
		$colgroup->addElement('col',['style' => 'width:95%']);
		$thead = $table->addElement('thead');
		$tr = $thead->addElement('tr');
		$tr->addElement('th',['class' => 'lhelc']);
		$tr->addElement('th',['class' => 'lhebl'],$this->GetTitle());
		$tbody = $table->addElement('tbody');
		foreach($options as $option_tag => $option_val):
			//	create a unique identifier for each row.
			//	use label tag for text column to allow enabling the radio button by clicking on the text
			$uuid = sprintf('radio_%s',uuid());
			$tr = $tbody->addElement('tr');
			$tdl = $tr->addElement('td',['class' => 'lcelc']);
			$attributes = [
				'name' => $ctrlname,
				'value' => $option_tag,
				'type' => 'radio',
				'id' => $uuid
			];
			if($value === $option_tag):
				$attributes['checked'] = 'checked';
			endif;
			$tdl->addElement('input',$attributes);
			$tdr = $tr->addElement('td',['class' => 'lcebl']);
			$tdr->addElement('label',['for' => $uuid],$option_val);
		endforeach;
	}
}
class HTMLMountComboBox2 extends HTMLComboBox2 {
	//	constructor
	function __construct($ctrlname,$title,$value,$description) {
		global $config;
		//	generate options.
		$a_mounts = &array_make_branch($config,'mounts','mount');
		array_sort_key($a_mounts,'devicespecialfile');
		$options = [];
		$options[''] = gtext('Must choose one');
		foreach($a_mounts as $r_mount):
			$options[$r_mount['uuid']] = $r_mount['sharename'];
		endforeach;
		parent::__construct($ctrlname,$title,$value,$options,$description);
	}
}
class HTMLTimeZoneComboBox2 extends HTMLComboBox2 {
	function __construct($ctrlname,$title,$value,$description) {
		//	get time zone data.
		function is_timezone($elt) {
			return !preg_match("/\/$/",$elt);
		}
		exec('/usr/bin/tar -tf /usr/share/zoneinfo.txz',$timezonelist);
		$timezonelist = array_filter($timezonelist,'is_timezone');
		sort($timezonelist);
		//	generate options.
		$options = [];
		foreach($timezonelist as $tzv):
			if(!empty($tzv)):
				$tzv = substr($tzv,2); // Remove leading './'
				$options[$tzv] = $tzv;
			endif;
		endforeach;
		parent::__construct($ctrlname,$title,$value,$options,$description);
	}
}
class HTMLLanguageComboBox2 extends HTMLComboBox2 {
	function __construct($ctrlname,$title,$value,$description) {
		global $g_languages;
		//	generate options.
		$options = [];
		foreach($g_languages as $key => $val):
			if('auto' == $key):
				$options[$key] = gtext('Autodetect');
			else:	
				$options[$key] = locale_get_display_name($key,$key);
			endif;
		endforeach;
		parent::__construct($ctrlname,$title,$value,$options,$description);
	}
}
class HTMLInterfaceComboBox2 extends HTMLComboBox2 {
	function __construct($ctrlname,$title,$value,$description) {
		global $config;
		//	generate options.
		$options = ['lan' => 'LAN'];
		for($i = 1;isset($config['interfaces']['opt' . $i]);$i++):
			if(isset($config['interfaces']['opt' . $i]['enable'])):
				$options['opt' . $i] = $config['interfaces']['opt' . $i]['descr'];
			endif;
		endfor;
		parent::__construct($ctrlname,$title,$value,$options,$description);
	}
}
class HTMLListBox2 extends HTMLMultiSelectControl2 {
	function __construct($ctrlname,$title,$value,$options,$description) {
		parent::__construct('formselect',$ctrlname,$title,$value,$options,$description);
	}
}
class HTMLCheckboxBox2 extends HTMLListBox2 {
	function ComposeInner(&$anchor) {
		$ctrlname = $this->GetCtrlName();
		$value = $this->GetValue();
		$options = $this->GetOptions();
		$table = $anchor->addElement('table',['class' => 'area_data_selection']);
		$colgroup = $table->addElement('colgroup');
		$colgroup->addElement('col',['style' => 'width:5%']);
		$colgroup->addElement('col',['style' => 'width:95%']);
		$thead = $table->addElement('thead');
		$tr = $thead->addElement('tr');
		$tr->addElement('th',['class' => 'lhelc']);
		$tr->addElement('th',['class' => 'lhebl'],$this->GetTitle());
		$tbody = $table->addElement('tbody');
		foreach($options as $option_tag => $option_val):
			//	create a unique identifier for each row.
			//	use label tag for text column to allow toggling the checkbox button by clicking on the text
			$uuid = sprintf('checkbox_%s',uuid());
			$tr = $tbody->addElement('tr');
			$tdl = $tr->addElement('td',['class' => 'lcelc']);
			$attributes = [
				'name' => sprintf('%s[]',$ctrlname),
				'value' => $option_tag,
				'type' => 'checkbox',
				'id' => $uuid
			];
			if(is_array($value) && in_array($option_tag,$value)):
				$attributes['checked'] = 'checked';
			endif;
			$tdl->addElement('input',$attributes);
			$tdr = $tr->addElement('td',['class' => 'lcebl']);
			$tdr->addElement('label',['for' => $uuid],$option_val);
		endforeach;
	}
}
class HTMLSeparator2 extends HTMLBaseControl2 {
	var $_colspan = 2;
	var $_classseparator = 'gap';
	// constructor
	function __construct() {
	}
	//	get/set methods
	function SetClassSeparator($cssclass) {
		$this->_classseparator = $cssclass;
	}
	function GetClassSeparator() {
		return $this->_classseparator;
	}
	function SetColSpan($colspan) {
		$this->_colspan = $colspan;
	}
	function GetColSpan() {
		return $this->_colspan;
	}
	//	support methods
	function GetClassOfSeparator() {
		return $this->GetClassSeparator();
	}
	function Compose(DOMNode &$anchor = NULL) {
		//	create root DOM if anchor not provided
		if(is_null($anchor)):
			$anchor = new co_DOMDocument();
		endif;
		//	helping variables
		$ctrlname = $this->GetCtrlName();
		//	compose
		$attributes = [];
		if(preg_match('/\S/',$ctrlname)):
			$attributes['id'] = $ctrlname;
		endif;
		$o_tr = $anchor->addElement('tr',$attributes);
		$attributes = ['class' => $this->GetClassOfSeparator(),'colspan' => $this->GetColSpan()];
		$o_tr->addElement('td',$attributes);
		return $anchor;
	}
}
class HTMLTitleLine2 extends HTMLBaseControl2 {
	var $_colspan = 2;
	var $_classtopic = 'lhetop';
	//	constructor
	function __construct($title) {
		$this->SetTitle($title);
	}
	//	get/set methods
	function SetClassTopic($cssclass) {
		$this->_classtopic = $cssclass;
	}
	function GetClassTopic() {
		return $this->_classtopic;
	}
	function SetColSpan($colspan) {
		$this->_colspan = $colspan;
	}
	function GetColSpan() {
		return $this->_colspan;
	}
	//	support methods
	function GetClassOfTopic() {
		return $this->GetClassTopic();
	}
	function Compose(DOMNode &$anchor = NULL) {
		//	create root DOM if anchor not provided
		if(is_null($anchor)):
			$anchor = new co_DOMDocument();
		endif;
		//	helping variables
		$ctrlname = $this->GetCtrlName();
		//	compose
		$attributes = [];
		if(preg_match('/\S/',$ctrlname)):
			$attributes['id'] = $ctrlname;
		endif;
		$tr = $anchor->addElement('tr',$attributes);
		$attributes = ['class' => $this->GetClassOfTopic(),'colspan' => $this->GetColSpan()];
		$th = $tr->addElement('th',$attributes,$this->GetTitle());
		return $anchor;
	}
}
class HTMLTitleLineCheckBox2 extends HTMLCheckBox2 {
	var $_colspan = 2;
	var $_classtopic = 'lhetop';
	//	constructor
	function __construct($ctrlname,$title,$value,$caption) {
		parent::__construct($ctrlname,$title,$value,$caption);
	}
	//	get/set methods
	function SetClassTopic($cssclass) {
		$this->_classtopic = $cssclass;
	}
	function GetClassTopic() {
		return $this->_classtopic;
	}
	function SetColSpan($colspan) {
		$this->_colspan = $colspan;
	}
	function GetColSpan() {
		return $this->_colspan;
	}
	//	support methods
	function GetClassOfTopic() {
		return $this->GetClassTopic();
	}
	function Compose(DOMNode &$anchor = NULL) {
		//	create root DOM if anchor not provided
		if(is_null($anchor)):
			$anchor = new co_DOMDocument();
		endif;
		//	helping variables
		$ctrlname = $this->GetCtrlName();
		//	compose
		$attributes = ['id' => sprintf('%s_tr',$ctrlname)];
		$tr = $anchor->addElement('tr',$attributes);	
		$attributes = ['class' => $this->GetClassOfTopic(),'colspan' => $this->GetColSpan()];
		$th = $tr->addElement('th',$attributes);
		$attributes = ['style' => 'float:left'];
		$spanleft = $th->addElement('span',$attributes,$this->GetTitle());
		$attributes = ['style' => 'float:right'];
		$spanright = $th->addElement('span',$attributes);
		$label = $spanright->addElement('label');
		$attributes = ['type' => 'checkbox','id' => $ctrlname,'name' => $ctrlname,'class' => 'formfld cblot','value' => 'yes'];
		$this->getAttributes($attributes);
		$label->addElement('input',$attributes);
		$attributes = ['class' => 'cblot'];
		$label->addElement('span',$attributes,$this->GetCaption());
		return $anchor;
	}
}
class HTMLText2 extends HTMLBaseControl2 {
	//	constructor
	function __construct($ctrlname,$title,$text) {
		$this->SetCtrlName($ctrlname);
		$this->SetReadOnly(true);
		$this->SetRequired(false);
		$this->SetTitle($title);
		$this->SetValue($text);
	}
	//	support methods
	function ComposeInner(&$anchor) {
		//	compose
		$anchor->addElement('span',[],$this->GetValue());
	}
}
class HTMLTextInfo2 extends HTMLBaseControl2 {
	function __construct($ctrlname,$title,$text) {
		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetValue($text);
	}
	function Compose(DOMNode &$anchor = NULL) {
		//	create root DOM if anchor not provided
		if(is_null($anchor)):
			$anchor = new co_DOMDocument();
		endif;
		//	helping variables
		$ctrlname = $this->GetCtrlName();
		//	compose
		$attributes = ['id' => sprintf('%s_tr',$ctrlname)];
		$tr = $anchor->addElement('tr',$attributes);
		$attributes = ['class' => $this->GetClassOfTag()];
		$tdtag = $tr->addElement('td',$attributes,$this->GetTitle());
		$attributes = ['class' => $this->GetClassOfData()];
		$tddata = $tr->addElement('td',$attributes);
		$attributes = ['id' => $ctrlname];
		$tddata->addElement('span',$attributes,$this->getValue());
		return $anchor;
	}
}
class HTMLRemark2 extends HTMLBaseControl2 {
	function __construct($ctrlname,$title,$text) {
		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetValue($text);
	}
	function Compose(DOMNode &$anchor = NULL) {
		//	create root DOM if anchor not provided
		if(is_null($anchor)):
			$anchor = new co_DOMDocument();
		endif;
		//	helping variables
		$ctrlname = $this->GetCtrlName();
		//	compose
		$attributes = ['id' => $ctrlname];
		$div1 = $anchor->addElement('div',$attributes);
		$attributes = ['class' => 'red'];
		$div1->addElement('strong',$attributes,$this->GetTitle());
		$attributes = [];
		$div2 = $anchor->addElement('div',$attributes,$this->GetValue());
		return $anchor;
	}
}
class HTMLFolderBox2 extends HTMLBaseControl2 {
	var $_path = '';

	function __construct($ctrlname,$title,$value,$description = '') {
		parent::__construct($ctrlname,$title,$value,$description);
	}
	function GetPath() {
		return $this->_path;
	}
	function SetPath($path) {
		$this->_path = $path;
	}
	function ComposeInner(&$anchor) {
		//	helping variables
		$ctrlname = $this->GetCtrlName();
		$ctrlnamedata = $ctrlname . 'data';
		$value = $this->GetValue();
		//	control code for folders
		$t = [];
		$t[] = sprintf('function onchange_%s() {',$ctrlname);
		$t[] = "\t" . sprintf('document.getElementById("%s").value = document.getElementById("%s").value;',$ctrlnamedata,$ctrlname);
		$t[] = '}';
		$t[] = sprintf('function onclick_add_%s() {',$ctrlname);
		$t[] = "\t" . sprintf('var value = document.getElementById("%s").value;',$ctrlnamedata);
		$t[] = "\t" . 'if (value != "") {';
		$t[] = "\t\t" . 'var found = false;';
		$t[] = "\t\t" . sprintf('var element = document.getElementById("%s");',$ctrlname);
		$t[] = "\t\t" . 'for (var i = 0; i < element.length; i++) {';
		$t[] = "\t\t\t" . 'if (element.options[i].text == value) {';
		$t[] = "\t\t\t\t" . 'found = true;';
		$t[] = "\t\t\t\t" . 'break;';
		$t[] = "\t\t\t" . '}';
		$t[] = "\t\t" . '}';
		$t[] = "\t\t" . 'if (found != true) {';
		$t[] = "\t\t\t" . 'element.options[element.length] = new Option(value, value, false, true);';
		$t[] = "\t\t\t" . sprintf('document.getElementById("%s").value = "";',$ctrlnamedata);
		$t[] = "\t\t" . '}';
		$t[] = "\t" . '}';
		$t[] = '}';
		$t[] = sprintf('function onclick_delete_%s() {',$ctrlname);
		$t[] = "\t" . sprintf('var element = document.getElementById("%s");',$ctrlname);
		$t[] = "\t" . 'if (element.value != "") {';
		$t[] = "\t\t" . sprintf('var msg = confirm("%s");',gtext('Do you really want to remove the selected item from the list?'));
		$t[] = "\t\t" . 'if (msg == true) {';
		$t[] = "\t\t\t" . 'element.options[element.selectedIndex] = null;';
		$t[] = "\t\t\t" . sprintf('document.getElementById("%s").value = "";',$ctrlnamedata);
		$t[] = "\t\t" . '}';
		$t[] = "\t" . '} else {';
		$t[] = "\t\t" . sprintf('alert("%s");',gtext('Select item to remove from the list'));
		$t[] = "\t" . '}';
		$t[] = '}';
		$t[] = sprintf('function onclick_change_%s() {',$ctrlname);
		$t[] = "\t" . sprintf('var element = document.getElementById("%s");',$ctrlname);
		$t[] = "\t" . 'if (element.value != "") {';
		$t[] = "\t\t" . sprintf('var value = document.getElementById("%s").value;',$ctrlnamedata);
		$t[] = "\t\t" . 'element.options[element.selectedIndex].text = value;';
		$t[] = "\t\t" . 'element.options[element.selectedIndex].value = value;';
		$t[] = "\t" . '}';
		$t[] = '}';
		$t[] = sprintf('function onsubmit_%s() {',$ctrlname);
		$t[] = "\t" . sprintf('var element = document.getElementById("%s");',$ctrlname);
		$t[] = "\t" . 'for (var i = 0; i < element.length; i++) {';
		$t[] = "\t\t" . 'if (element.options[i].value != "")';
		$t[] = "\t\t\t" . 'element.options[i].selected = true;';
		$t[] = "\t" . '}';
		$t[] = '}';
		$anchor->addJavaScript(implode(PHP_EOL,$t));
		//	section 1: select + delete
		$div1 = $anchor->addElement('div');
		//	selected folder
		$attributes = [
			'id' => $ctrlname,
			'name' => sprintf('%s[]',$ctrlname),
			'class' => 'formfld',
			'multiple' => 'multiple',
			'size' => '4',
			'style' => 'width:350px',
			'onchange' => sprintf('onchange_%s()',$ctrlname)
		];
		$select = $div1->addElement('select',$attributes);
		foreach ($value as $value_key => $value_val):
			$attributes = ['value' => $value_val];
			$select->addElement('option',$attributes,$value_val);
		endforeach;
		//	delete button
		$attributes = [
			'type' => 'button',
			'id' => sprintf('%sdeletebtn',$ctrlname),
			'name' => sprintf('%sdeletebtn',$ctrlname),
			'class' => 'formbtn',
			'value' => gtext('Delete'),
			'onclick' => sprintf('onclick_delete_%s()',$ctrlname)
		];
		$div1->addElement('input',$attributes);
		//	section 2: choose, add + change
		$div2 = $anchor->addElement('div');
		//	path input field
		$attributes = [
			'type' => 'text',
			'id' => sprintf('%sdata',$ctrlname),
			'name' => sprintf('%sdata',$ctrlname),
			'class' => 'formfld',
			'value' => '',
			'size' => 60
		];			
		$div2->addElement('input',$attributes);
		//	choose button
		$js = sprintf('ifield = form.%s;',$ctrlnamedata)
			. ' filechooser = window.open("filechooser.php'
			. '?p="+encodeURIComponent(ifield.value)+"'
			. sprintf('&sd=%s",',$this->GetPath())
			. ' "filechooser",'
			. ' "scrollbars=yes,toolbar=no,menubar=no,statusbar=no,width=550,height=300");'
			. ' filechooser.ifield = ifield;'
			. ' window.ifield = ifield;';
		$attributes = [
			'type' => 'button',
			'id' => sprintf('%sbrowsebtn',$ctrlname),
			'name' => sprintf('%sbrowsebtn',$ctrlname),
			'class' => 'formbtn',
			'value' => '...',
			'onclick' => $js
		];
		$div2->addElement('input',$attributes);
		//	add button
		$attributes = [
			'type' => 'button',
			'id' => sprintf('%saddbtn',$ctrlname),
			'name' => sprintf('%saddbtn',$ctrlname),
			'class' => 'formbtn',
			'value' => gtext('Add'),
			'onclick' => sprintf('onclick_add_%s()',$ctrlname) 
		];
		$div2->addElement('input',$attributes);
		//	change button
		$attributes = [
			'type' => 'button',
			'id' => sprintf('%schangebtn',$ctrlname),
			'name' => sprintf('%schangebtn',$ctrlname),
			'class' => 'formbtn',
			'value' => gtext('Change'),
			'onclick' => sprintf('onclick_change_%s()',$ctrlname) 
		];
		$div2->addElement('input',$attributes);
	}
}
class HTMLFolderBox12 extends HTMLFolderBox2 {
	function ComposeInner(&$anchor) {
		//	helping variables
		$ctrlname = $this->GetCtrlName();
		$ctrlnamedata = $ctrlname . 'data';
		$ctrlnamefiletype = $ctrlname . 'filetype';
		$value = $this->GetValue();
		//	control code for folders
		$t = [];
		$t[] = 'function onchange_' . $ctrlname . '() {';
		$t[] = "\t" . 'var value1 = document.getElementById("' . $ctrlname . '");';
		$t[] = "\t" . 'if (value1.value.charAt(0) != "/") {';
		$t[] = "\t" . 'document.getElementById("' . $ctrlnamedata . '").value = value1.value.substring(2,(value1.value.length));';
		$t[] = "\t" . 'document.getElementById("' . $ctrlnamefiletype . '").value = value1.value.charAt(0);';
		$t[] = "\t\t" . '}else{';
		$t[] = "\t" . 'document.getElementById("' . $ctrlnamedata . '").value = document.getElementById("' . $ctrlname . '").value;';
		$t[] = "\t" . 'document.getElementById("' . $ctrlnamefiletype . '").value = "";';
		$t[] = "\t" . '}';
		$t[] = '}';
		$t[] = 'function onclick_add_' . $ctrlname . '() {';
		$t[] = "\t" . 'var value1 = document.getElementById("' . $ctrlnamedata . '").value;';
		$t[] = "\t" . 'var valuetype = document.getElementById("' . $ctrlnamefiletype . '").value;';
		$t[] = "\t" . 'if (valuetype != "") {';
		$t[] = "\t\t" . 'var valuetype = valuetype + ",";';
		$t[] = "\t" . '}';
		$t[] = "\t" . 'var value = valuetype +  value1;';
		$t[] = "\t" . 'if (value != "") {';
		$t[] = "\t\t" . 'var found = false;';
		$t[] = "\t\t" . 'var element = document.getElementById("' . $ctrlname . '");';
		$t[] = "\t\t" . 'for (var i = 0; i < element.length; i++) {';
		$t[] = "\t\t\t" . 'if (element.options[i].text == value) {';
		$t[] = "\t\t\t\t" . 'found = true;';
		$t[] = "\t\t\t\t" . 'break;';
		$t[] = "\t\t\t" . '}';
		$t[] = "\t\t" . '}';
		$t[] = "\t\t" . 'if (found != true) {';
		$t[] = "\t\t\t" . 'element.options[element.length] = new Option(value, value, false, true);';
		$t[] = "\t\t\t" . 'document.getElementById("' . $ctrlnamedata . '").value = "";';
		$t[] = "\t\t" . '}';
		$t[] = "\t" . '}';
		$t[] = '}';
		$t[] = 'function onclick_delete_' . $ctrlname . '() {';
		$t[] = "\t" . 'var element = document.getElementById("' . $ctrlname . '");';
		$t[] = "\t" . 'if (element.value != "") {';
		$t[] = "\t\t" . 'var msg = confirm("' . gtext('Do you really want to remove the selected item from the list?') . '");';
		$t[] = "\t\t" . 'if (msg == true) {';
		$t[] = "\t\t\t" . 'element.options[element.selectedIndex] = null;';
		$t[] = "\t\t\t" . 'document.getElementById("' . $ctrlnamedata . '").value = "";';
		$t[] = "\t\t" . '  }';
		$t[] = "\t" . '} else {';
		$t[] = "\t\t" . 'alert("' . gtext('Select item to remove from the list') . '");';
		$t[] = "\t" . '}';
		$t[] = '}';
		$t[] = 'function onclick_change_' . $ctrlname . '() {';
		$t[] = "\t" . 'var element = document.getElementById("' . $ctrlname . '");';
		$t[] = "\t" . 'if (element.value != "") {';
		$t[] = "\t\t" . 'var value1 = document.getElementById("' . $ctrlnamedata . '").value;';
		$t[] = "\t" . 'var valuetype = document.getElementById("' . $ctrlnamefiletype . '").value;';
		$t[] = "\t" . 'if (valuetype != "") {';
		$t[] = "\t\t" . 'var valuetype = valuetype + ",";';
		$t[] = "\t" . '}';
		$t[] = "\t" . 'var value = valuetype +  value1;';
		$t[] = "\t\t" . 'element.options[element.selectedIndex].text = value;';
		$t[] = "\t\t" . 'element.options[element.selectedIndex].value = value;';
		$t[] = "\t" . '}';
		$t[] = '}';
		$t[] = 'function onsubmit_' . $ctrlname . '() {';
		$t[] = "\t" . 'var element = document.getElementById("' . $ctrlname . '");';
		$t[] = "\t" . 'for (var i = 0; i < element.length; i++) {';
		$t[] = "\t\t" . 'if (element.options[i].value != "")';
		$t[] = "\t\t\t" . 'element.options[i].selected = true;';
		$t[] = "\t" . '}';
		$t[] = '}';
		$anchor->addJavaScript(implode(PHP_EOL,$t));
		//	section 1: select + delete
		$div1 = $anchor->addElement('div');
		//	selected folder
		$attributes = ['id' => $ctrlname,'name' => sprintf('%s[]',$ctrlname),'class' => 'formfld','multiple' => 'multiple','style' => 'width:350px','onchange' => sprintf('onchange_%s()',$ctrlname)];
		$select = $div1->addElement('select',$attributes);
		foreach ($value as $value_key => $value_val):
			$attributes = ['value' => $value_val];
			$select->addElement('option',$attributes,$value_val);
		endforeach;
		//	delete button
		$attributes = [
			'type' => 'button',
			'id' => sprintf('%sdeletebtn',$ctrlname),
			'name' => sprintf('%sdeletebtn',$ctrlname),
			'class' => 'formbtn',
			'value' => gtext('Delete'),
			'onclick' => sprintf('onclick_delete_%s()',$ctrlname)
		];
		$div1->addElement('input',$attributes);
		//	section 2: choose, add + change
		$div2 = $anchor->addElement('div');
		//	media type
		$attributes = ['id' => sprintf('%sfiletype',$ctrlname),'name' => sprintf('%sfiletype',$ctrlname),'class' => 'formfld'];
		$select = $div2->addElement('select',$attributes);
		$attributes = ['value' => '','selected' => 'selected'];
		$select->addElement('option',$attributes,gtext('All'));
		$attributes = ['value' => 'A'];
		$select->addElement('option',$attributes,gtext('Audio'));
		$attributes = ['value' => 'V'];
		$select->addElement('option',$attributes,gtext('Video'));
		$attributes = ['value' => 'P'];
		$select->addElement('option',$attributes,gtext('Pictures'));
		//	path input field
		$attributes = [
			'type' => 'text',
			'id' => sprintf('%sdata',$ctrlname),
			'name' => sprintf('%sdata',$ctrlname),
			'class' => 'formfld',
			'value' => '',
			'size' => 60
		];			
		$div2->addElement('input',$attributes);
		//	choose button
		$js = sprintf('ifield = form.%s;',$ctrlnamedata)
			. ' filechooser = window.open("filechooser.php'
			. '?p="+encodeURIComponent(ifield.value)+"'
			. sprintf('&sd=%s",',$this->GetPath())
			. ' "filechooser",'
			. ' "scrollbars=yes,toolbar=no,menubar=no,statusbar=no,width=550,height=300");'
			. ' filechooser.ifield = ifield;'
			. ' window.ifield = ifield;';
		$attributes = [
			'type' => 'button',
			'id' => sprintf('%sbrowsebtn',$ctrlname),
			'name' => sprintf('%sbrowsebtn',$ctrlname),
			'class' => 'formbtn',
			'value' => '...',
			'onclick' => $js
		];
		$div2->addElement('input',$attributes);
		//	add button
		$attributes = [
			'type' => 'button',
			'id' => sprintf('%saddbtn',$ctrlname),
			'name' => sprintf('%saddbtn',$ctrlname),
			'class' => 'formbtn',
			'value' => gtext('Add'),
			'onclick' => sprintf('onclick_add_%s()',$ctrlname) 
		];
		$div2->addElement('input',$attributes);
		//	change button
		$attributes = [
			'type' => 'button',
			'id' => sprintf('%schangebtn',$ctrlname),
			'name' => sprintf('%schangebtn',$ctrlname),
			'class' => 'formbtn',
			'value' => gtext('Change'),
			'onclick' => sprintf('onclick_change_%s()',$ctrlname) 
		];
		$div2->addElement('input',$attributes);
	}
}
trait co_DOMTools {
/**
 *	Appends a child node to an element
 *	@param string $name
 *	@param array $attributes
 *	@param string $value
 *	@param string $namespaceURI
 *	@return DOMNode $subnode
 */
	public function addElement(string $name,array $attributes = [],string $value = NULL,string $namespaceURI = NULL) {
		$subnode = $this->appendChild(new co_DOMElement($name,NULL,$namespaceURI));
		$subnode->addAttributes($attributes);
		if(preg_match('/\S/',$value)):
			$saved_setting = libxml_use_internal_errors(true); // user cares about exceptions
			$document = $subnode->ownerDocument ?? $this;
			$innerhtml = $document->createDocumentFragment(); // create fragment from $value
			if($innerhtml->appendXML($value)):
				$subnode->appendChild($innerhtml);
			endif;
			libxml_clear_errors();
			libxml_use_internal_errors($saved_setting);
		endif;
		return $subnode;
	}
/**
 *	Inserts a child node on top of the children
 *	@param string $name
 *	@param array $attributes
 *	@param string $value
 *	@param string $namespaceURI
 *	@return DOMNode $subnode
 */
	public function prepend_element(string $name,array $attributes = [],string $value = NULL,string $namespaceURI = NULL) {
		if(is_null($this->firstChild)):
			$subnode = $this->insertBefore(new co_DOMElement($name,NULL,$namespaceURI));
		else:
			$subnode = $this->insertBefore(new co_DOMElement($name,NULL,$namespaceURI),$this->firstChild);
		endif;
		$subnode->addAttributes($attributes);
		if(preg_match('/\S/',$value)):
			$saved_setting = libxml_use_internal_errors(true); // user cares about exceptions
			$innerhtml = $subnode->ownerDocument->createDocumentFragment(); // create fragment from $value
			if($innerhtml->appendXML($value)):
				$subnode->appendChild($innerhtml);
			endif;
			libxml_clear_errors();
			libxml_use_internal_errors($saved_setting);
		endif;
		return $subnode;
	}
/**
 *	Appends a JavaScript node to the DOM
 *	@param string $text
 *	@return DOMNode $this
 */
	public function addJavaScript(string $text = '') {
		$node = $this->addElement('script');
		if(false !== $node):
			$opening = $node->ownerDocument->createTextNode(PHP_EOL . '//![CDATA[' . PHP_EOL);
			if(false !== $opening):
				$node->appendChild($opening);
				$cdata = $node->ownerDocument->createCDATASection($text);
				if(false !== $cdata):
					$node->appendChild($cdata);
					$ending = $node->ownerDocument->createTextNode(PHP_EOL . '//]]>' . PHP_EOL);
					if(false !== $ending):
						$node->appendChild($ending);
					endif;
				endif;
			endif;
		endif;
		return $this;
	}
	//	tags
	public function putCOL(array $attributes = []) {
		$this->addElement('col',$attributes);
		return $this;
	}
	public function putIMG(array $attributes = []) {
		$this->addElement('img',$attributes);
		return $this;
	}
	public function addA(array $attributes = [],string $value = NULL) {
		$subnode = $this->addElement('a',$attributes,$value);
		return $subnode;
	}
	public function addDIV(array $attributes = [],string $value = NULL) {
		$subnode = $this->addElement('div',$attributes,$value);
		return $subnode;
	}
	public function addFORM(array $attributes = []) {
		$subnode = $this->addElement('form',$attributes);
		return $subnode;
	}
	public function addLI(array $attributes = [],string $value = NULL) {
		$subnode = $this->addElement('li',$attributes,$value);
		return $subnode;
	}
	public function addP(array $attributes = [],string $value = NULL) {
		$subnode = $this->addElement('p',$attributes,$value);
		return $subnode;
	}
	public function addSPAN(array $attributes = [],string $value = NULL) {
		$subnode = $this->addElement('span',$attributes,$value);
		return $subnode;
	}
	public function addUL(array $attributes = []) {
		$subnode = $this->addElement('ul',$attributes);
		return $subnode;
	}
	public function addTABLE(array $attributes = []) {
		$subnode = $this->addElement('table',$attributes);
		return $subnode;
	}
	public function addCOLGROUP(array $attributes = []) {
		$subnode = $this->addElement('colgroup',$attributes);
		return $subnode; 
	}
	public function addTHEAD(array $attributes = []) {
		$subnode = $this->addElement('thead',$attributes);
		return $subnode;
	}
	public function addTBODY(array $attributes = []) {
		$subnode = $this->addElement('tbody',$attributes);
		return $subnode;
	}
	public function addTFOOT(array $attributes = []) {
		$subnode = $this->addElement('tfoot',$attributes);
		return $subnode;
	}
	public function addTR(array $attributes = []) {
		$subnode = $this->addElement('tr',$attributes);
		return $subnode;
	}
	public function addTD(array $attributes = [],string $value = NULL) {
		$subnode = $this->addElement('td',$attributes,$value);
		return $subnode;
	}
	public function addTH(array $attributes = [],string $value = NULL) {
		$subnode = $this->addElement('th',$attributes,$value);
		return $subnode;
	}
	//	tab menu fragments and macros
/**
 * 
 *	@return DOMNode $subnode
 */
	public function add_tabnav_area() {
		$table_attributes = [
			'id' => 'area_navigator'
		];
		$document = $this->ownerDocument ?? $this;
		$target = $document->getElementById('g4h');
		if(isset($target)):
			$append_mode = true; // last element of header section
			$div_attributes = [
				'id' => 'area_tabnav',
				'style' => 'padding: 0px 25px 10px 25px;'
			];
			if($append_mode):
				$subnode = $target->
					addDIV($div_attributes)->
						addTABLE($table_attributes)->
							addTBODY();
			else:
				$subnode = $target->
					prepend_element('div',$div_attributes)->
						addTABLE($table_attributes)->
							addTBODY();
			endif;
		else: // workaround for unconverted pages because of padding
			$target = $this;
			$subnode = $target->
				addTABLE($table_attributes)->
					addTBODY();
		endif;
		return $subnode;
	}
/**
 *	Creates tags for upper navigation menu 
 *	@return DOMNode $subnode
 */
	public function add_tabnav_upper() {
		$subnode = $this->
			addTR()->
				addTD(['class' => 'tabnavtbl'])->
					addUL(['id' => 'tabnav']);
		return $subnode;
	}
/**
 *	Creates tags for lower navigation menu 
 *	@return DOMNode
 */
	public function add_tabnav_lower() {
		$subnode = $this->
			addTR()->
				addTD(['class' => 'tabnavtbl'])->
					addUL(['id' => 'tabnav2']);
		return $subnode;
	}
/**
 *	Adds a menu item to the navigation menu
 *	@param string $href Link to script
 *	@param string $value Name of the menu item
 *	@param string $title Title of the menu item
 *	@param bool $active Flag to indicate an active menu item
 *	@return object $this
 */
	public function mount_tabnav_record(string $href = '',string $value = '',string $title = '',bool $active = false) {
		$attributes = [];
		if(preg_match('/\S/',$href)):
			$attributes['href'] = $href;
		endif;
		if(preg_match('/\S/',$title)):
			$attributes['title'] = $title;
		endif;
		$this->
			addLI(['class' => $active ? 'tabact' : 'tabinact'])->
				addA($attributes)->
					addSPAN([],$value);
		return $this;
	}
	/**
	 * 
	 * @return object $subnode
	 */
	public function add_area_data() {
		$subnode = $this->
			addTABLE(['id' => 'area_data'])->
				addTBODY()->
					addTR()->
						addTD(['id' => 'area_data_frame']);
		return $subnode;
	}
	public function mount_input_errors(array $input_errors = []) {
		foreach($input_errors as $input_error):
			if(is_string($input_error)):
				if(preg_match('/\S/',$input_error)):
					$messages[] = $input_error;
				endif;
			endif;
		endforeach;
		if(!empty($messages)):
			$ul = $this->
				addDIV(['id' => 'errorbox'])->
					addTABLE(['border' => '0','cellspacing' => '0','cellpadding' => '1','width' => '100%'])->
						addTR()->
							addTD(['class' => 'icon','align' => 'center','valign' => 'center'])->
								putIMG(['src' => 'images/error_box.png','alt' => ''])->
								parentNode->
							addTD(['class' => 'message'])->
								addDIV([],sprintf('%s:',gtext('The following input errors were detected'),':'))->
									addUL();
			foreach($messages as $message):
				$ul->addLI([],$message);
			endforeach;
		endif;
		return $this;
	}
	public function helper_core_box(string $type,string $message = '') {
		if(preg_match('/\S/',$message)):
			switch($type):
				case 'error': $id = 'errorbox';$img = 'error_box.png';break;
				case 'info': $id = 'infobox';$img = 'info_box.png';break;
				case 'warning': $id = 'warningbox';$img = 'warn_box.png';break;
			endswitch;
			$this->
				addDIV(['id' => $id])->
					addTABLE(['border' => '0','cellspacing' => '0','cellpadding' => '1','width' => '100%'])->
						addTR()->
							addTD(['class' => 'icon','align' => 'center','valign' => 'center'])->
								putIMG(['src' => sprintf('/images/%s',$img),'alt' => ''])->
								parentNode->
							addTD(['class' => 'message'],$message);
		endif;
		return $this;
	}
	public function mount_error_box(string $message = '') {
		$this->helper_core_box('error',$message);
		return $this;
	}
	public function mount_info_box(string $message = '') {
		$this->helper_core_box('info',$message);
		return $this;
	}
	public function mount_warning_box(string $message = '') {
		$this->helper_core_box('warning',$message);
		return $this;
	}
	//	data settings table macros
	public function add_table_data_settings() {
		$subnode = $this->addTABLE(['class' => 'area_data_settings']);
		return $subnode;
	}
	/**
	 * 
	 *	@return DOMNode $this
	 */
	public function mount_colgroup_data_settings() {
		$this->mount_colgroup_with_classes(['area_data_settings_col_tag','area_data_settings_col_data']);
		return $this;
	}
	public function mount_colgroup_with_classes(array $data = []) {
		$colgroup = $this->addCOLGROUP();
		foreach($data as $value):
			$colgroup->putCOL(['class' => htmlspecialchars($value)]);
		endforeach;
		return $this;
	}
	public function mount_colgroup_with_styles(string $tag,array $data = []) {
		$colgroup = $this->addCOLGROUP();
		$tag = htmlspecialchars($tag);
		foreach($data as $value):
			$colgroup->putCOL(['style' => sprintf('%s:%s;',$tag,htmlspecialchars($value))]);
		endforeach;
		return $this;
	}
	//	title macros
	public function mount_titleline($title,$colspan = 2,$ctrlname = '') {
		$ctrl = new HTMLTitleLine2($title);
		$ctrl->SetColSpan($colspan);
		$ctrl->SetCtrlName($ctrlname);
		$ctrl->Compose($this);
		return $this;
	}
	public function mount_titleline_with_checkbox(properties $p,$value,int $colspan = 2) {
		$ctrl = new HTMLTitleLineCheckBox2($p->get_id(),$p->get_title(),$value,$p->get_caption());
		$ctrl->SetColSpan($colspan);
		$ctrl->Compose($this);
		return $this;
	}
	//	elements
	public function mount_checkbox(properties $p,$value,bool $required = false,bool $readonly = false,$altpadding = false) {
		$ctrl = new HTMLCheckBox2($p->get_id(),$p->get_title(),$value,$p->get_caption(),$p->get_description());
		$ctrl->SetRequired($required);
		$ctrl->SetReadOnly($readonly);
		$ctrl->SetAltPadding($altpadding);
		$ctrl->Compose($this);
		return $this;
	}
	public function mount_input_text(properties $p,$value,bool $required = false,bool $readonly = false,$size = 40,$altpadding = false,$maxlength = 0,string $placeholder = '') {
		$ctrl = new HTMLEditBox2($p->get_id(),$p->get_title(),$value,$p->get_description(),$size);
		$ctrl->SetRequired($required);
		$ctrl->SetReadOnly($readonly);
		$ctrl->SetAltPadding($altpadding);
		$ctrl->SetMaxLength($maxlength);
		$ctrl->SetPlaceholder($placeholder);
		$ctrl->Compose($this);
		return $this;
	}
	public function mount_radio_grid(properties $p,$value,bool $required = false,bool $readonly = false,$onclick = '') {
		$ctrl = new HTMLRadioBox2($p->get_id(),$p->get_title(),$value,$p->get_options(),$p->get_description());
		$ctrl->SetRequired($required);
		$ctrl->SetReadOnly($readonly);
		$ctrl->SetJSonClick($onclick);
		$ctrl->Compose($this);
		return $this;
	}
	public function mount_select(properties $p,$value,bool $required = false,bool $readonly = false,$onclick = '') {
		$ctrl = new HTMLComboBox2($p->get_id(),$p->get_title(),$value,$p->get_options(),$p->get_description());
		$ctrl->SetRequired($required);
		$ctrl->SetReadOnly($readonly);
		$ctrl->SetJSonClick($onclick);
		$ctrl->Compose($this);
		return $this;
	}
	public function mount_separator($colspan = 2,$ctrlname = '') {
		$ctrl = new HTMLSeparator2();
		$ctrl->SetColSpan($colspan);
		$ctrl->SetCtrlName($ctrlname);
		$ctrl->Compose($this);
		return $this;
	}
	public function mount_textinfo(string $id,string $title = '',$value) {
		$ctrl = new HTMLTextInfo2($id,$title,$value);
		$ctrl->Compose($this);
		return $this;
	}
	//	submit area macros
	public function add_button_area() {
		$root = $this->ownerDocument ?? $this;
		$target = $root->getElementById('g4f') ?? $this;
		$append_mode = false; // top element of footer area
		$div_attributes = ['id' => 'submit','style' => 'padding: 0px 25px 0px 25px;'];
		if($append_mode):
			$subnode = $target->addDIV($div_attributes);
		else:
			$subnode = $target->prepend_element('div',$div_attributes);
		endif;
		return $subnode;
	}
	public function mount_button_submit(string $value = NULL,string $content = NULL,string $id = NULL) {
		$element      = 'button';
		$class_button = 'formbtn';
		$value        = $value ?? 'cancel';
		$id           = $id ?? sprintf('%1$s_%2$s',$element,$value);
		$content      = $content ?? gtext('Cancel');
		$attributes   = ['name' => 'submit','type' => 'submit','class' => $class_button,'value' => $value,'id' => $id];
		$this->addElement($element,$attributes,$content);
		return $this;
	}
	public function mount_button_cancel() {
		$this->mount_button_submit('cancel',gtext('Cancel'));
		return $this;
	}
	public function mount_button_edit() {
		$this->mount_button_submit('edit',gtext('Edit'));
		return $this;
	}
	public function mount_button_save() {
		$this->mount_button_submit('save',gtext('Apply'));
		return $this;
	}
	//	remark area macros
	public function add_remarks() {
		$subnode = $this->addDIV(['id' => 'remarks']);
		return $subnode;
	}
	public function mount_remark($ctrlname,$title,$text) {
		$ctrl = new HTMLRemark2($ctrlname,$title,$text);
		$ctrl->Compose($this);
		return $this;
	}
	public function mount_authtoken() {
		$this->addElement('input',['name' => 'authtoken','type' => 'hidden','value' => Session::getAuthToken()]);
		return $this;
	}
	public function clc_page_title(array $page_title = []) {
		$output = implode(htmlspecialchars(' · '),$page_title);
		return $output;
	}
	public function clc_html_page_title(array $page_title = []) {
		$output = htmlspecialchars(system_get_hostname());
		if(!empty($page_title)):
			$output .= htmlspecialchars(' - ');
			$output .= $this->clc_page_title($page_title);
		endif;
		return $output;
	}
	public function mount_head(array $page_title = [],bool $requires_datechooser = false) {
		$head = $this->addElement('head',['id' => 'head']);
		$head->addElement('meta',['charset' => system_get_language_codeset()]);
		$head->addElement('meta',['name' => 'format-detection','content' => 'telephone=no']);
		$head->addElement('title',[],$this->clc_html_page_title($page_title));
		$head->addElement('link',['href' => '/css/gui.css','rel' => 'stylesheet','type' => 'text/css']);
		$head->addElement('link',['href' => '/css/navbar.css','rel' => 'stylesheet','type' => 'text/css']);
		$head->addElement('link',['href' => '/css/tabs.css','rel' => 'stylesheet','type' => 'text/css']);	
		$head->addElement('script',['type' => 'text/javascript','src' => '/js/jquery.min.js']);
		$head->addElement('script',['type' => 'text/javascript','src' => '/js/gui.js']);
		$head->addElement('script',['type' => 'text/javascript','src' => '/js/spinner.js']);
		$head->addElement('script',['type' => 'text/javascript','src' => '/js/spin.min.js']);
		if($requires_datechooser):
			$head->addElement('link',['href' => 'js/datechooser.css','rel' => 'stylesheet','type' => 'text/css']);
			$head->addElement('script',['type' => 'text/javascript','src' => 'js/datechooser.js']);
		endif;
		return $this;
	}
	/**
	 *	Creates the body element of the page with all basic subnodes.
	 *	
	 *	@param array $page_title
	 *	@param string $action_url If $action_url empty no form element will be created.
	 *	@return DOMNode $this
	 */
	public function mount_body(array $page_title = [],string $action_url = NULL) {
		$jdata = <<<EOJ
$(window).on("load", function() {
	$("#tabnav").on('click', function() { spinner(); });
	$("#tabnav2").on('click', function() { spinner(); });
	$("#iform").submit(function() { spinner(); });
	$(".spin").click(function() { spinner(); });
});
EOJ;
		$body = $this->addElement('body',['id' => 'main']);
		if(isset($action_url) && preg_match('/\S/',$action_url)):
			$form_attributes = ['action' => $action_url,'method' => 'post','id' => 'iform','name' => 'iform'];
			$flexcontainer = $body->addFORM($form_attributes)->addDIV(['id' => 'pagebodyflex']);
		else:
			$flexcontainer = $body->addDIV(['id' => 'pagebodyflex']);
		endif;
		$flexcontainer->addDIV(['id' => 'spinner_main']);
		$flexcontainer->addDIV(['id' => 'spinner_overlay','style' => 'display: none; background-color: white; position: fixed; left:0; top:0; height:100%; width:100%; opacity: 0.25;']);
		$flexcontainer->mount_header($page_title);
		$flexcontainer->mount_main();
		$flexcontainer->mount_footer();
		$flexcontainer->addJavascript($jdata);
		return $this;
	}
	public function mount_header(array $page_title = []) {
		$header = $this->addElement('header',['id' => 'g4h']);
		if(!$_SESSION['g']['shrinkpageheader']):
			$header->
				addDIV(['id' => 'header'])->
					addDIV(['id' => 'headerrlogo'])->
						addDIV(['class' => 'hostname'])->
							addSPAN([],system_get_hostname())->
								parentNode->
							parentNode->
						parentNode->
					addDIV(['id' => 'headerlogo'])->
						addA(['title' => sprintf('www.%s',get_product_url()),'href' => sprintf('https://www.%s',get_product_url()),'target' => '_blank'])->
							putIMG(['src' => '/images/header_logo.png','alt' => 'logo']);
		endif;
		$header->addDIV(['id' => 'area_navhdr'],make_headermenu());
		$header->addDIV(['id' => 'gapheader']);
		if(!empty($page_title)):
			$header->addP(['class' => 'pgtitle','style' => 'padding:0px 25px 0px 25px;' ],$this->clc_page_title($page_title));
		endif;
		return $this;
	}
	public function mount_main() {
		$this->
			addElement('main',['id' => 'g4m'])->
			addDIV(['id' => 'pagecontent']);
		return $this;
	}
	/**
	 * 
	 * @global array $g
	 * @return $this
	 */
	public function mount_footer() {
		global $g;
		
		$g4fx = $this->
			addElement('footer',['id' => 'g4f'])->
				addDIV(['id' => 'gapfooter'])->
					parentNode->
				addDIV(['id' => 'pagefooter'])->
					add_table_data_settings()->
						addCOLGROUP()->
							putCOL(['style' => 'width:10%'])->
							putCOL(['style' => 'width:80%'])->
							putCOL(['style' => 'width:10%'])->
							parentNode->
						addTBODY()->
							addTR();
		$g4fl = $g4fx->addTD(['class' => 'g4fl']);
		if(Session::isAdmin()):
			if(file_exists(sprintf('%s/sysreboot.reqd',$g['varrun_path']))):
				$g4fl->
					addA(['class' => 'g4fi','href' => '/reboot.php'])->
						putIMG(['src' => '/images/notify_reboot.png','title' => gtext('A reboot is required'),'alt' => gtext('Reboot Required')]);
			endif;
		endif;
		$g4fc = $g4fx->addTD(['class' => 'g4fc'],htmlspecialchars(get_product_copyright()));
		$g4fr = $g4fx->addTD(['class' => 'g4fr']);
		return $this;
	}
}
class co_DOMElement extends \DOMElement implements ci_DOM {
	use co_DOMTools;
	
	public function addAttributes($attributes = []) {
		foreach($attributes as $key => $value):
			$this->setAttribute($key,$value);
		endforeach;
		return $this;
	}
}
class co_DOMDocument extends \DOMDocument implements ci_DOM {
	use co_DOMTools;
	
	public function __construct(string $version = '1.0',string $encoding = 'UTF-8') {
		parent::__construct($version,$encoding);
		$this->preserveWhiteSpace = false;
		$this->formatOutput = true;
		$this->registerNodeClass('DOMElement','co_DOMElement');
	}
	public function render() {
		echo $this->saveHTML();
		return $this;
	}
	public function get_html() {
		return $this->saveHTML();
	}
/*
	public function getElementById($id) {
		$xpath = new DOMXPath($this);
		return $xpath->query("//*[@id='$id']")->item(0);
	}
 */
}
interface ci_DOM {
}
function new_page(array $page_title = [],string $action_url = NULL) {
	$document = new co_DOMDocument();
	
	$document->
		loadHTML('<!DOCTYPE HTML>',LIBXML_HTML_NOIMPLIED);
	$document->
		addElement('html')->
			mount_head($page_title)->
			mount_body($page_title,$action_url);
	return $document;
}
?>
