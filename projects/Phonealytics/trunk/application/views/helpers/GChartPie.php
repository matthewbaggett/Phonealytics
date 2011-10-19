<?php
require_once("GChartBase.php");
class Zend_View_Helper_GChartPie extends Zend_View_Helper_GChartBase 
{
	const RENDER_TWODEE = false;
	const RENDER_THREEDEE = true;
    public function GChartPie ($str_div, $array_of_pie_values,$options = array())
    {
    	$this->str_div = $str_div;
    	$this->str_function_name = $this->_getFunctionName();
    	
    	$default_options = array(
    		'title' => '',
    		'title_axis_x' => 'Toppings',
    		'title_axis_y' => 'Slices',
    		'width' => 400,
    		'height' => 300,
    		'is3D' => Zend_View_Helper_GChartPie::RENDER_TWODEE,
    		'backgroundColor' => 'transparent',
    	);
    	
    	$options = array_merge($default_options, $options);
    	$script[] = "// Make chart for $this->str_div";
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
    	foreach($array_of_pie_values as $key => $value){
    		$i++;
    		$int_value = (int) $value;
      		$script[] = "	['{$key}', {$int_value}]" . ($i==count($array_of_pie_values)?'':',');
      	}
      	$script[] = "	]);";
		$script[] = "	";
      	$script[] = "	// Set chart options";
      	$script[] = "	var options = {";
      	$script[] = "		'title':	'{$options['title']}',";
      	$script[] = "		'width':	{$options['width']},";
      	$script[] = "		'height':	{$options['height']},";
      	$script[] = "		'is3D':		'".($options['is3D']?'true':'false')."',";
      	$script[] = "		'backgroundColor': '{$options['backgroundColor']}',";
		$script[] = "	}	";
      	$script[] = "	// Instantiate and draw our chart, passing in some options.";
      	$script[] = "	var chart = new google.visualization.PieChart(document.getElementById('{$this->str_div}'));";
      	$script[] = "	chart.draw(data, options);";
      	$script[] = "}";
      	
      	
      	$str_script = implode("\n",$script)."\n";
    	$this->view->headScript()->appendScript($str_script,'text/javascript');
    	
    	echo "<div id=\"{$this->str_div}\"></div>\n";
    }
}