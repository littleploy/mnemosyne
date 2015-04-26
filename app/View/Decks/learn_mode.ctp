<?php

	echo $this->Html->css("learn_mode.css");
	echo $this->Html->script("learn_mode.js");

?>
<div class="row">
	<h5 id="header">
		Learning - 
	</h5>
</div>
<div class="row" id="card-container">
	  <div class="preloader-wrapper big active">
	  	<div class="spinner-layer spinner-red-only">
	  	<div class="circle-clipper left">
        <div class="circle"></div>
      	</div>
      	<div class="gap-patch">
	        <div class="circle"></div>
      	</div>
      		<div class="circle-clipper right">
        		<div class="circle"></div>
      		</div>
    	</div>
  	</div>
	<div class="top-bottom-padding-adjustment">
		<div class="card left-card">
			<div class="col z-depth-1 card-wrapper">
				<div class="front">
					<p>front</p>
					<p>test</p>
					<p>test</p>
					<p>test</p>
					<button class="waves-effect waves-teal btn-flat">
						<i class="mdi-action-flip-to-back left"></i>Answer
					</button>
				</div>
				<div class="back">
					<p>back</p>
					<p>test</p>
					<p>test</p>
					<p>test</p>
					<button class="waves-effect waves-teal btn-flat">
						<i class="mdi-action-flip-to-back left"></i>Question
					</button>
				</div>
			</div>
		</div>
		<div class="card middle-card">
			<div class="col z-depth-1 card-wrapper">
				<div class="front">
					<p>front</p>
					<p>test</p>
					<p>test</p>
					<p>test</p>
					<button class="waves-effect waves-teal btn-flat">
						<i class="mdi-action-flip-to-back left"></i>Answer
					</button>
				</div>
				<div class="back">
					<p>back</p>
					<p>test</p>
					<p>test</p>
					<p>test</p>
					<button class="waves-effect waves-teal btn-flat">
						<i class="mdi-action-flip-to-back left"></i>Question
					</button>
				</div>
			</div>
		</div>
		<div class="card right-card">
			<div class="col z-depth-1 card-wrapper">
				<div class="front">
					<p>front</p>
					<p>test</p>
					<p>test</p>
					<p>test</p>
					<button class="waves-effect waves-teal btn-flat">
						<i class="mdi-action-flip-to-back left"></i>Answer
					</button>
				</div>
				<div class="back">
					<p>back</p>
					<p>test</p>
					<p>test</p>
					<p>test</p>
					<button class="waves-effect waves-teal btn-flat">
						<i class="mdi-action-flip-to-back left"></i>Question
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col s4 offset-s4">
		<div class="center-align top-bottom-padding-adjustment">
			<a class="waves-effect waves-light btn" id="btn-back"><i class="mdi-navigation-arrow-back left"></i> Back</a>
			<a class="waves-effect waves-light btn" id="btn-next">Next <i class="mdi-navigation-arrow-forward right"></i></a>
		</div>
	</div>
</div>