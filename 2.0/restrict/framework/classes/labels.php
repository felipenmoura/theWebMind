<?php
	class Label
	{
		public $name= null;
		public $content= null;
		
		public function __construct($name, $content)
		{
			$this->name= $name;
			$this->content= $content;
		}
	}
	class Labels
	{
		public $labels= Array();
		public function addLabel($name, $content)
		{
			$this->labels[$name]= new Label($name, $content);
		}
		public function create($labelType= 'IFRAME')
		{
			$c=0;
			?>
							<table class='gridTable'>
								<tr>
									<td class='guiasBar'>
			<?php
				foreach($this->labels as $label)
				{
					if($c==0)
						$className= 'guiasFocus';
					else
						$className= 'guias';
					$c++;
			?>
										<table class='<?php echo $className; ?>'
											   onclick="top.setFocusOnLabel(this, document.getElementById('<?php echo $label->name; ?>Iframe'), '<?php echo $labelType; ?>');">
											<tr>
												<td class='guiaLeft'>
													<br/>
												</td>
												<td class='guiaCenter'>
													<?php echo $label->name; ?>
												</td>
												<td class='guiaRight'>
													<br/>
												</td>
											</tr>
										</table>
			<?php
				}
			?>
									</td>
								</tr>
								<tr>
									<td class='guiasBody'>
			<?php
				$c= 0;
				foreach($this->labels as $label)
				{
					if($c==0)
						$display= 'block';
					else
						$display= 'none';
					$c++;
			?>
										<div id='<?php echo $label->name; ?>Iframe'
											 style='display: <?php echo $display; ?>;
													width: 100%;
													height: 100%;'
											 labelElemente='true'>
											<?php
												echo $label->content;
											?>
										</div>
			<?php
				}
			?>
									</td>
								</tr>
							</table>
						<?php
		}
	}
?>