<?php
// PieChart class is used for creating pie charts in the main amCharts class object.
class PieChart{
	public $name = "chartdiv";	// id to use on chart's div
	public $title = "Item";
	public $value = "Value";

	public $data=array();
	public $width=600;
	public $height=400;
	public $width_units='px';
	public $height_units='px';
	public $left_margin;
	public $right_margin;
	public $colors = array('595A31', '8CA400', 'F29513', 'FFC080', '643E3B', 'C09376', 'FAB252', 'EEC5C3', 'FFFF7D', '3A3F4B', '911FF7', '584A28', 'D23000', '050C1B','450900');

	// TODO: Make all variables modifiable with a function / multiple functions
	function __construct($title = false, $value = false, $name = false){
		if ($title)
			$this->title = $title;
		if ($value)
			$this->value = $value;
		if ($name)
			$this->name = $name;
	}
	// Adds data to the chart object. Must be an array.
	public function add($data)
	{
		if (is_array($data))
			array_push($this->data, $data);
	}
	// Returns the javascript variable declarations to create the chart. To be used by the main amCharts() class object.
	public function get_js_vars()
	{
		return "var ".$this->name."_chart; var ".$this->name."_legend; ";
	}
	// Returns the javascript initialization code to create the chart. To be used by the main amCharts() class object.
	public function get_js_init($legend = true)
	{
		$chart_var = $this->name."_chart";
		$legend_var = $this->name."_legend";
		$init = '
			'.$chart_var.' = new AmCharts.AmPieChart();
			'.$chart_var.'.dataProvider = '.$this->name.'_data;
			'.$chart_var.'.titleField = "'.$this->title.'";
			'.$chart_var.'.valueField = "'.$this->value.'";

			// - Enable Later?
			//'.$chart_var.'.marginTop = 35;
			//'.$chart_var.'.marginLeft = 100;
			//'.$chart_var.'.color = "#7a7a7a";
			//'.$chart_var.'.backgroundColor = "#FFFFFF";
			//'.$chart_var.'.backgroundAlpha = 1;

			// Make these modular in the future?
			'.$chart_var.'.startDuration = 0;	// No loading animations
			'.$chart_var.'.labelRadius = -25;	// lables inside the pie chunks
			'.$chart_var.'.labelText = "[[percents]]%";	// template for the labels
			';
		// Only create legend if $legend == true (defaults to true)
		if ($legend)
			$init .= '

			// Make this modular in the future?
			'.$legend_var.' = new AmCharts.AmLegend();
			'.$legend_var.'.align = "center";
			'.$legend_var.'.markerType = "circle";
			'.$chart_var.'.addLegend('.$legend_var.');
			';
		$init .= '

			'.$chart_var.'.write("'.$this->name.'");
		';

		return $init;
	}
	// Returns the data used to create the chart. To be used by the main amCharts() class object.
	public function get_js_data()
	{
		$data_string = "
		var ".$this->name."_data = [";
		foreach ($this->data as $row)
		{
			foreach ($row as $title_data=>$value_data)
			{
				$data_string .= '{'.$this->title.':"'.$title_data.'",'.$this->value.':"'.$value_data.'"},';
			}
		}
		$data_string = rtrim($data_string,',');	// trim the trailing comma from the final entry.

		$data_string .= "];
		";
		return $data_string;
	}
	// Returns the <div> tag that will hold the chart on-screen. Call within the <body> of your page.
	public function get_chart_div()
	{
		echo '<div id="'.$this->name.'" style="width:'.$this->width.''.$this->width_units.'; height:'.$this->height.''.$this->height_units.';"></div>';
	}
	// Returns a color for the chart data to use.
	private function get_color()
	{
		$rand = rand(0, count($this->colors)-1);
		$count=0;
		foreach($this->colors as $id=>$color)
		{
			if($count==$rand)
			{
				$mycolor = $color;
				unset($this->colors[$id]);
			}
			$count+=1;
		}

		return $mycolor;
	}
}
// amCharts class is a wrapper/handler for managing more than one amChart on a single page.
class amCharts {
	public $charts = array();

	function __construct()
	{
		// TODO: Do we need anything here? If not, remove!
	}
	// Creates a new chart object. `$handle` must be provided to store a reference to the specific object this creates, and can then be used in the page's code to add data to it.
	public function addChart(&$handle, $type="pie", $title=false, $value=false, $name=false)	//$type defaults to "pie" because that is currently the only type available.
	{
		if (!$name)
			$name = "chartdiv".(count($this->charts)+1);

		switch($type)
		{
			case "pie":
				$handle = $this->charts[] = new PieChart($title, $value, $name);
				break;
			default:
				$handle = false;
				break;
		}
	}
	// Outputs the required include statements for the charts. Use in the <head> of your document.
	public function get_js_includes()
	{
		$url = $this->get_server_path(1,false,true,true);
		echo '<script src="'.$url.'js/amcharts.js" type="text/javascript"></script>
		<script src="'.$url.'js/raphael.js" type="text/javascript"></script>
		';
	}
	// Outputs the required javascript code for creating all of the charts on the page. Use in the <head> of your document.
	public function get_js_chart()
	{
		$vars = $data = $init = '';	// Initialize the variables.
		foreach ($this->charts as $chart)
		{
			$vars .= $chart->get_js_vars();
			$data .= $chart->get_js_data();
			$init .= $chart->get_js_init();
		}
		echo '
		<script type="text/javascript">
		'.$vars.'
		'.$data.'
		window.onload = function() {
			'.$init.'
		}
		</script>
		';
	}

	// Returns the current server directory. // TODO: MIGHT BE MOVED TO ANOTHER CLASS IN THE FUTURE.
	public function get_server_path($steps = 0, $suffix = false, $url = false, $protocol = false)
	{
		// Get the current subdirectory.
		//$current_subdir = substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],"/")+1); //Grabs PHP_SELF and removes the filename.
		$current_subdir = str_replace( basename($_SERVER['SCRIPT_FILENAME']), '', $_SERVER['PHP_SELF'] );
		// Get the current filename path of this filename.
		$target_directory = dirname(__FILE__);
		// If $steps is set, cycle through and step out however many times given.
		if ($steps)
		{
			for ($step = 0; $step < $steps; $step++)
			$target_directory = dirname($target_directory);
		}
		// Append a trailing slash to the directory.
		$target_directory .= "/";
		// Go to the root of the web server path. Removes the local filesystem prefix.
		$path = substr($target_directory,strrpos($target_directory,$current_subdir));
		// If a suffix is provided, append it to the end-result.
		if ($suffix)
			$path .= $suffix;
		// If URL is set to true
		if ($url)
		{
			$path = $_SERVER['HTTP_HOST'].$path;
			if ($protocol)
			{
				$protocol_array = explode('/',$_SERVER['SERVER_PROTOCOL']);
				$protocol_string = strtolower($protocol_array[0]).'://';
				$path = $protocol_string.$path;
			}
		}

		return $path;
	}
}
?>