<?php
require_once("GChartBase.php");
class Zend_View_Helper_GChartBar extends Zend_View_Helper_GChartBase
{
	
    public function GChartBar ($str_div, $array_of_bar_values,$options = array())
    {
    	$this->str_div = $str_div;
    	$this->str_function_name = $this->_getFunctionName();
    	
    	$default_options = array(
    		'title' => '',
    		'title_axis_x' => 'Toppings',
    		'title_axis_y' => 'Slices',
    		'width' => 400,
    		'height' => 300,
    		'legend' => 'right', //right,top,bottom,in,none,
    		'backgroundColor' => 'transparent',
    	);
    	
    	$options = array_merge($default_options, $options);
    	
    	$script = array();
    	$script[] = "// Make chart for {$this->str_div}";
    	$script[] = "// Set a callback to run when the Google Visualization API is loaded.";
      	$script[] = "google.setOnLoadCallback({$this->str_function_name});";
      	$script[] = " ";
      	$script[] = "// Callback that creates and populates a data table, ";
      	$script[] = "// instantiates the pie chart, passes in the data and";
      	$script[] = "// draws it.";
      	$script[] = "function {$this->str_function_name}() {";
		$script[] = "	";
      	$script[] = "	// Create the data table.";
      	$script[] = "	var data = new google.visualization.DataTable();";
     
    	
      	$script[] = "	data.addColumn('string', '{$options['title_axis_x']}');";
      	$script[] = "	data.addColumn('number', '{$options['title_axis_y']}');";
      	$script[] = "	data.addRows([";
      	$i = 0;
      	//print_r($array_of_bar_values);exit;
    	foreach($array_of_bar_values as $key => $value){
    		$i++;
    		$int_value = (int) $value;
    		$key = addslashes($key);
      		$script[] = "	['{$key}', {$int_value}]" . ($i==count($array_of_bar_values)?'':',');
      	}
      	$script[] = "	]);";
		$script[] = "	";
      	$script[] = "	// Set chart options";
      	$script[] = "	var options = {";
      	$script[] = "		'title':	'{$options['title']}',";
      	$script[] = "		'width':	{$options['width']},";
      	$script[] = "		'height':	{$options['height']},";
      	$script[] = "		'vAxis': {";
      	$script[] = "			'title': '{$options['title_axis_x']}',";
      	$script[] = "		},";
      	$script[] = "		'hAxis': {";
      	$script[] = "			'title': '{$options['title_axis_y']}',";
      	$script[] = "		},";
      	$script[] = "		'legend': '{$options['legend']}',";
      	$script[] = "		'backgroundColor': '{$options['backgroundColor']}',";
		$script[] = "	}	";
      	$script[] = "	// Instantiate and draw our chart, passing in some options.";
      	$script[] = "	var chart = new google.visualization.BarChart(document.getElementById('{$this->str_div}'));";
      	$script[] = "	chart.draw(data, options);";
      	$script[] = "}";
      	
      	
      	$str_script = implode("\n",$script)."\n";
    	$this->view->headScript()->appendScript($str_script,'text/javascript');
    	
    	echo "<div id=\"{$this->str_div}\"></div>\n";
    }
}