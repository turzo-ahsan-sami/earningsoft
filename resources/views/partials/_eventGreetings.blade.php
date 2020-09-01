<div class="events-grettings-bar" style="background-color:green;color:#000;font-size:11px;height:25px;width:100%;">
	<div style="width:1000px;margin:0 auto;">
		<h3>ঈদ মোবারক। সারা দেশে চলছে ঈদের উৎসব। ঈদ মানে আনন্দ, ঈদ মানে খুশি। ঈদ মানে হাজার কষ্টের মাঝেও একটুখানি হাসি। আম্বালা আইটির পক্ষ থেকে সবাইকে ঈদুল ফিতর এর শুভেছা । ঈদ মোবারক।</h3>
		{{-- <span style="padding:5px 0;">
			<span><img src="{{ asset('images/eid-mubarak.gif') }}" width="25" height="25" /></span>
			<span style="padding:0 20px" id="clock">&nbsp;</span>			
			<span id="minibarDate"></span>
		</span> --}}
	</div>
</div>
<style>
.events-grettings-bar {
	height: 50px;	
	overflow: hidden;
	position: relative;
	background: red;
}
.events-grettings-bar h3 {
	font-size: 15px;
	color: #FFF;
	position: absolute;
	width: 100%;
	height: 100%;
	margin: 0;
	padding: 3px 0;
	_line-height: 50px;
	text-align: center;
	/* Starting position */
	-moz-transform:translateX(100%);
	-webkit-transform:translateX(100%);	
	transform:translateX(100%);
	/* Apply animation to this element */	
	-moz-animation: events-grettings-bar 30s linear infinite;
	-webkit-animation: events-grettings-bar 30s linear infinite;
	animation: events-grettings-bar 30s linear infinite;
}
/* Move it (define the animation) */
@-moz-keyframes events-grettings-bar {
 0%   { -moz-transform: translateX(100%); }
 100% { -moz-transform: translateX(-100%); }
}
@-webkit-keyframes events-grettings-bar {
 0%   { -webkit-transform: translateX(100%); }
 100% { -webkit-transform: translateX(-100%); }
}
@keyframes events-grettings-bar {
 0%   { 
 -moz-transform: translateX(100%); /* Firefox bug fix */
 -webkit-transform: translateX(100%); /* Firefox bug fix */
 transform: translateX(100%); 		
 }
 100% { 
 -moz-transform: translateX(-100%); /* Firefox bug fix */
 -webkit-transform: translateX(-100%); /* Firefox bug fix */
 transform: translateX(-100%); 
 }
}
</style>
