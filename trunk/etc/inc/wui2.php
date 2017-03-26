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
		//	description
		//	string
		//	[string, string, ...]
		//	[ [string], [string], ... ]
		//	[ [string, color], [string, color], ...]
		//	[ [string, color, no_br], [string, color, no_br], ...]
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
								if($suppressbr):
									$suppressbr = false;
								else:
									$description_output .= '<br />';
								endif;
								$description_output .= $description_row[0];
								break;
							case 3: // allow not to break
								$suppressbr = (is_bool($description_row[2])) ? $description_row[2] : $suppressbr;
							case 2:
								if($suppressbr):
									$suppressbr = false;
								else:
									$description_output .= '<br />';
								endif;
								if(is_null($description_row[1])):
									$description_output .= $description_row[0];
								else:
									$description_output .= sprintf('<font color="%2$s">%1$s</font>',$description_row[0],$description_row[1]);
								endif;
								break;
						endswitch;
					endif;
				endforeach;
			endif;
		endif;
		return $description_output;
	}
	function Compose() {
		$ctrlname = $this->GetCtrlName();
		$description = $this->GetDescriptionOutput();
/*
		echo sprintf('<tr id="%s_tr">',$ctrlname),"\n";
		echo sprintf('<td class="%1$s"><label for="%2$s">%3$s</label></td>',$this->GetClassOfTag(),$ctrlname,$this->GetTitle()),"\n";
		echo sprintf('<td class="%s">',$this->GetClassOfData()),"\n";
		$this->ComposeInner();
		if (!empty($description)):
			echo sprintf('<br /><span class="tagabout">%s</span>',$description),"\n";
		endif;
		echo '</td>',"\n";
		echo '</tr>',"\n";
 */		
		//	root DOM
		$root = new co_DOMDocument();
		//	compose
		$attributes = ['id' => sprintf('%s_tr',$ctrlname)];
		$tr = $root->addElement('tr',$attributes);
		$attributes = ['class' => $this->GetClassOfTag()];
		$tdtag = $tr->addElement('td',$attributes);
		$attributes = ['for' => $ctrlname];
		$tdtag->addElement('label',$attributes,$this->GetTitle());
		$attributes = ['class' => $this->GetClassOfData()];
		$tddata = $tr->addElement('td',$attributes);
		$this->ComposeInner($tddata);
		if(!empty($description)):
			$tddata->addElement('br');
			$attributes = ['class' => 'tagabout'];
			$tddata->addElement('span',$attributes,$description);
		endif;
		//	showtime
		return $root;
	}
	function ComposeInner(&$root) {
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
	function ComposeInner(&$root) {
		$attributes = [
			'type' => 'text',
			'id' => $this->GetCtrlName(),
			'name' => $this->GetCtrlName(),
			'class' => $this->GetClassOfInputText(),
			'size' => $this->GetSize(),
			'value' => htmlspecialchars($this->GetValue(),ENT_QUOTES),
		];
		$this->GetAttributes($attributes);
		$root->addElement('input',$attributes);
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
	function ComposeInner(&$root) {
		$ctrlname = $this->GetCtrlName();
		$attributes = [
			'type' => 'password',
			'id' => $ctrlname,
			'name' => $ctrlname,
			'class' => $this->GetClassOfInputPassword(),
			'size' => $this->GetSize(),
			'value'=> htmlspecialchars($this->GetValue(),ENT_QUOTES),
		];
		$this->GetAttributes($attributes);
		$root->addElement('input',$attributes);
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
	function ComposeInner(&$root) {
		$ctrlname = $this->GetCtrlName();
		$ctrlnameconf = $this->GetCtrlNameConf();
		$attributes = [
			'type' => 'password',
			'id' => $ctrlname,
			'name' => $ctrlname,
			'class' => $this->GetClassOfInputPassword(),
			'size' => $this->GetSize(),
			'value'=> htmlspecialchars($this->GetValue(),ENT_QUOTES)
		];
		$this->GetAttributes($attributes);
		$o_div1 = $root->addElement('div');
		$o_div1->addElement('input',$attributes);
		$attributes = [
			'type' => 'password',
			'id' => $ctrlnameconf,
			'name' => $ctrlnameconf,
			'class' => $this->GetClassOfInputPassword(),
			'size' => $this->GetSize(),
			'value' => htmlspecialchars($this->GetValueConf(),ENT_QUOTES)
		];
		$this->GetAttributesConfirm($attributes);
		$o_div2 = $root->addElement('div');
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
	function ComposeInner(&$root) {
		$ctrlname = $this->GetCtrlName();
		$attributes = [
			'id' => $ctrlname,
			'name' => $ctrlname,
			'class' => $this->GetClassOfTextarea(),
			'cols' => $this->GetColumns(),
			'rows' => $this->GetRows()
		];
		$this->GetAttributes($attributes);
		$root->addElement('textarea',$attributes,htmlspecialchars($this->GetValue(),ENT_QUOTES));
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
	function ComposeInner(&$root) {
		//	helper variables
		$ctrlname = $this->GetCtrlName();
		$size = $this->GetSize();
		//	input element
		$attributes = [
			'type' => 'text',
			'id' => $ctrlname,
			'name' => $ctrlname,
			'class' => 'formfld',
			'value' => htmlspecialchars($this->GetValue(),ENT_QUOTES),
			'size' => $size
		];
		$this->GetAttributes($attributes);
		$root->addElement('input',$attributes);
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
		$root->addElement('input',$attributes);
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
	function ComposeInner(&$root) {
		$ctrlname = $this->GetCtrlName();
		$ctrlnamenetmask = $this->GetCtrlNameNetmask();
		$valuenetmask = htmlspecialchars($this->GetValueNetmask(),ENT_QUOTES);
		$attributes = ['type' => 'text','id' => $ctrlname,'name' => $ctrlname,'class' => 'formfld','value' => htmlspecialchars($this->GetValue(),ENT_QUOTES),'size' => $this->GetSize()];
		$root->addElement('input',$attributes);
		$slash = $root->createTextNode(' / ');
		$root->appendChild($slash);
		$attributes = ['id' => $ctrlnamenetmask,'name' => $ctrlnamenetmask,'class' => 'formfld'];
		$o_select = $root->addElement('select',$attributes);
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
	function ComposeInner(&$root) {
		$ctrlname = $this->GetCtrlName();
		$ctrlnamenetmask = $this->GetCtrlNameNetmask();
		$attributes = [
			'type' => 'text',
			'id' => $ctrlname,
			'name' => $ctrlname,
			'class' => 'formfld',
			'value' => htmlspecialchars($this->GetValue(),ENT_QUOTES),
			'size' => $this->GetSize()
		];
		$root->addElement('input',$attributes);
		$slash = $root->createTextNode(' / ');
		$root->appendChild($slash);
		$attributes = [
			'type' => 'text',
			'id' => $ctrlnamenetmask,
			'name' => $ctrlnamenetmask,
			'class' => 'formfld',
			'value' => htmlspecialchars($this->GetValueNetmask(),ENT_QUOTES),
			'size' => 2
		];
		$root->addElement('input',$attributes);
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
	function ComposeInner(&$root) {
		//	helper variables
		$ctrlname = $this->GetCtrlName();
		//	compose
		$div = $root->addElement('div',['class' => $this->GetClassOfCheckbox()]);
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
	function ComposeInner(&$root) {
		$ctrlname = $this->GetCtrlName();
		$value = htmlspecialchars($this->GetValue(),ENT_QUOTES);
		$options = $this->GetOptions();
		$attributes = [
			'id' => $ctrlname,
			'name' => $ctrlname,
			'class' => $this->GetCtrlClass()
		];
		$this->GetAttributes($attributes);
		$select = $root->addElement('select',$attributes);
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
	function ComposeInner(&$root) {
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
		$select = $root->addElement('select',$attributes);
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
		foreach($g_languages as $languagek => $languagev):
			$options[$languagek] = $languagev['desc.localized'];
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
	function Compose() {
		//	root DOM
		$root = new co_DOMDocument();
		//	helping variables
		$ctrlname = $this->GetCtrlName();
		//	compose
		$attributes = [];
		if(preg_match('/\S/',$ctrlname)):
			$attributes['id'] = $ctrlname;
		endif;
		$o_tr = $root->addElement('tr',$attributes);
		$attributes = ['class' => $this->GetClassOfSeparator(),'colspan' => $this->GetColSpan()];
		$o_tr->addElement('td',$attributes);
		//	showtime
		return $root;
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
	function Compose() {
		//	root DOM
		$root = new co_DOMDocument();
		//	helping variables
		$ctrlname = $this->GetCtrlName();
		//	compose
		$attributes = [];
		if(preg_match('/\S/',$ctrlname)):
			$attributes['id'] = $ctrlname;
		endif;
		$tr = $root->addElement('tr',$attributes);
		$attributes = ['class' => $this->GetClassOfTopic(),'colspan' => $this->GetColSpan()];
		$th = $tr->addElement('th',$attributes,$this->GetTitle());
		//	showtime
		return $root;
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
	function Compose() {
		//	root DOM
		$root = new co_DOMDocument();
		//	helping variables
		$ctrlname = $this->GetCtrlName();
		//	compose
		$attributes = ['id' => sprintf('%s_tr',$ctrlname)];
		$tr = $root->addElement('tr',$attributes);	
		$attributes = ['class' => $this->GetClassOfTopic(),'colspan' => $this->GetColSpan()];
		$th = $tr->addElement('th',$attributes);
		$attributes = ['style' => 'float:left'];
		$spanleft = $th->addElement('span',$attributes,$this->GetTitle());
		$attributes = ['style' => 'float:right'];
		$spanright = $th->addElement('span',$attributes);
		$attributes = ['for' => $ctrlname,'style' => 'margin-right:8px;'];
		$label = $spanright->addElement('label',$attributes);
		$label->addElement('strong',[],$this->GetCaption());
		$attributes = ['type' => 'checkbox','id' => $ctrlname,'name' => $ctrlname,'class' => 'formfld','value' => 'yes','style' => 'vertical-align:middle;margin:0;'];
		$this->getAttributes($attributes);
		$checkbox = $spanright->addElement('input',$attributes);
		//	showtime
		return $root;
	}
}
class HTMLText2 extends HTMLBaseControl2 {
	//	constructor
	function __construct($ctrlname,$title,$text) {
		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetValue($text);
	}
	//	support methods
	function ComposeInner(&$root) {
		//	compose
		$root->addElement('span',[],$this->GetValue());
	}
}
class HTMLTextInfo2 extends HTMLBaseControl2 {
	function __construct($ctrlname,$title,$text) {
		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetValue($text);
	}
	function Compose() {
		//	root DOM
		$root = new co_DOMDocument();
		//	helping variables
		$ctrlname = $this->GetCtrlName();
		//	compose
		$attributes = ['id' => sprintf('%s_tr',$ctrlname)];
		$tr = $root->addElement('tr',$attributes);
		$attributes = ['class' => $this->GetClassOfTag()];
		$tdtag = $tr->addElement('td',$attributes,$this->GetTitle());
		$attributes = ['class' => $this->GetClassOfData()];
		$tddata = $tr->addElement('td',$attributes);
		$attributes = ['id' => $ctrlname];
		$tddata->addElement('span',$attributes,$this->getValue());
		//	showtime
		return $root;
	}
}
class HTMLRemark2 extends HTMLBaseControl2 {
	function __construct($ctrlname,$title,$text) {
		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetValue($text);
	}
	function Compose() {
		//	root DOM
		$root = new co_DOMDocument();
		//	helping variables
		$ctrlname = $this->GetCtrlName();
		//	compose
		$attributes = ['id' => $ctrlname];
		$div1 = $root->addElement('div',$attributes);
		$attributes = ['class' => 'red'];
		$div1->addElement('strong',$attributes,$this->GetTitle());
		$attributes = [];
		$div2 = $root->addElement('div',$attributes,$this->GetValue());
		//	showtime
		return $root;
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
	function ComposeInner(&$root) {
		//	helping variables
		$ctrlname = $this->GetCtrlName();
		$ctrlnamedata = $ctrlname . 'data';
		$value = $this->GetValue();
		//	control code for folders
		$t = [];
		$t[] = 'function onchange_' . $ctrlname . '() {';
		$t[] = "\t" . 'document.getElementById("' . $ctrlnamedata . '").value = document.getElementById("' . $ctrlname . '").value;';
		$t[] = '}';
		$t[] = 'function onclick_add_' . $ctrlname . '() {';
		$t[] = "\t" . 'var value = document.getElementById("' . $ctrlnamedata . '").value;';
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
		$t[] = "\t\t" . '}';
		$t[] = "\t" . '} else {';
		$t[] = "\t\t" . 'alert("' . gtext('Select item to remove from the list') . '");';
		$t[] = "\t" . '}';
		$t[] = '}';
		$t[] = 'function onclick_change_' . $ctrlname . '() {';
		$t[] = "\t" . 'var element = document.getElementById("' . $ctrlname . '");';
		$t[] = "\t" . 'if (element.value != "") {';
		$t[] = "\t\t" . 'var value = document.getElementById("' . $ctrlnamedata . '").value;';
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
		$root->addJavaScript(implode("\n",$t));
		//	section 1: select + delete
		$div1 = $root->addElement('div');
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
		$div2 = $root->addElement('div');
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
	function ComposeInner(&$root) {
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
		$root->addJavaScript(implode("\n",$t));
		//	section 1: select + delete
		$div1 = $root->addElement('div');
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
		$div2 = $root->addElement('div');
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
	public function addElement(string $name,array $attributes = [],string $value = NULL,string $namespaceURI = NULL) {
		$node = $this->appendChild(new co_DOMElement($name,NULL,$namespaceURI));
		$node->addAttributes($attributes);
		if(preg_match('/\S/',$value)):
			$innerhtml = $node->ownerDocument->createDocumentFragment(); // create fragment from $value
			if($innerhtml->appendXML($value)):
				$node->appendChild($innerhtml);
			endif;
		endif;
		return $node;
	}
	public function addJavaScript(string $text) {
		$node = $this->addElement('script');
		$newline = $node->ownerDocument->createTextNode("\n//");
		$node->appendChild($newline);
		$cdata = $node->ownerDocument->createCDATASection("\n" . $text . "\n//");
		$node->appendChild($cdata);
		return $node;
	}
}
class co_DOMImplementation extends \DOMImplementation {
	private $document;
	public function __construct() {
		return $this->createDocument();
	}
	public function __get($name) {
		return $this->doc->{$name};
	}
	public function __set($name,$value) {
		$this->doc->{$name}=$value;
	}
	public function __isset($name) {
		return isset($this->doc->{$name});
	}
	public function __unset($name) {
		return $this->doc->__unset($name);
	}
	public function __call($name,$args) {
		return call_user_func_array(array($this->doc,$name),$args);
	}
	public function createDocument($namespaceURI = NULL,$qualifiedName = NULL,DOMDocumentType $docType = NULL) {
		$this->document = parent::createDocument($namespaceURI,$qualifiedName,$docType);
		$this->document->xmlVersion='1.0';
		$this->document->xmlEncoding='UTF-8';
		$this->document->registerNodeClass('DOMDocument','co_DOMDocument');
//		$this->document->registerNodeClass('DOMDocumentFragment','co_DOMDocumentFragment');
		$this->document->registerNodeClass('DOMElement','co_DOMElement');
//		$this->document->registerNodeClass('DOMComment','co_DOMComment');
//		$this->document->registerNodeClass('DOMNode','co_DOMNode');
		return $this->document;
	}	
}
class co_DOMElement extends \DOMElement {
	use co_DOMTools;
	
	public function addAttributes($attributes = []) {
		foreach($attributes as $key => $value) {
			$this->setAttribute($key,$value);
		}
		return $this;
	}
}
class co_DOMDocument extends \DOMDocument {
	use co_DOMTools;
	
	public function __construct(string $version = '1.0',string $encoding = 'UTF-8') {
		parent::__construct($version,$encoding);
		$this->formatOutput = true;
	}
	public function flush() {
		echo $this->saveHTML();
	}
	public function render() {
		return $this->saveHTML();
	}
}
?>