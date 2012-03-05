<?php
// TODO: Modify to allow for multiple charts on a single page.  A single instance of the class must be made, an array of charts and their data must be created, and
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
	public function get_js_includes()
	{
		$url = $this->get_server_path(1,false,true,true);
		echo '<script src="'.$url.'js/amcharts.js" type="text/javascript"></script>
		<script src="'.$url.'js/raphael.js" type="text/javascript"></script>
		';
	}
	public function add($data)
	{
		if (is_array($data))
			array_push($this->data, $data);
	}
	public function get_js_chart($legend = true)
	{
		echo '
	<script type="text/javascript">
		var chart;
		var legend;
		';
		$this->get_data_js();
		echo '
		window.onload = function() {
			chart = new AmCharts.AmPieChart();
			chart.dataProvider = chartData;
			chart.titleField = "'.$this->title.'";
			chart.valueField = "'.$this->value.'";

			// - Enable Later?
			//chart.marginTop = 35;
			//chart.marginLeft = 100;
			//chart.color = "#7a7a7a";
			//chart.backgroundColor = "#FFFFFF";
			//chart.backgroundAlpha = 1;

			// Make these modular in the future?
			chart.startDuration = 0;	// No loading animations
			chart.labelRadius = -25;	// lables inside the pie chunks
			chart.labelText = "[[percents]]%";	// template for the labels
			';
		// Only create legend if $legend == true (defaults to true)
		if ($legend)
			echo '

			// Make this modular in the future?
			legend = new AmCharts.AmLegend();
			legend.align = "center";
			legend.markerType = "circle";
			chart.addLegend(legend);
			';
		echo '

			chart.write("'.$this->name.'");
		}

		</script>
		';
	}
	public function get_data_js()
	{
		/*echo '
		var chartData = [{hour:5,rev_cost:40},
				{hour:6,rev_cost:26.2},
				{hour:7,rev_cost:30.1},
				{hour:8,rev_cost:29.5},
				{hour:9,rev_cost:24.6}];
				';*/

		$data_string = "
		var chartData = [";
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
		echo $data_string;
	}
	public function get_chart_div()
	{
		echo '<div id="'.$this->name.'" style="width:'.$this->width.''.$this->width_units.'; height:'.$this->height.''.$this->height_units.';"></div>';
	}
	public function get_color()
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