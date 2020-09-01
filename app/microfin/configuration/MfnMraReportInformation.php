<?php

	namespace App\microfin\configuration;

	use Illuminate\Database\Eloquent\Model;

	class MfnMraReportInformation extends Model {

		public $timestamps = false;

		protected $table = 'mfn_mra_report_information';

		protected $fillable = ['periodDateFrom',
							   'periodDateTo', 
							   'gnrBodysExpirationDate', 
							   'gnrMale', 
							   'gnrFemale', 
							   'gnrTrans', 
							   'gnrNoOfYMeetingHe', 
							   'gnrLastMeetingDate', 
							   'gnrMemPreLastMeeting', 
							   'excutiveBodyExirationDate', 
							   'excutiveMale', 
							   'excutiveFemale', 
							   'excutiveTrans', 
							   'excutiveNoOfYMeetingHe', 
							   'excutiveLastMeetingDate', 
							   'excutiveMemPreLastMeeting', 
							   'serviceRules', 
							   'financialPolicy', 
							   'serviceCreditPolicy', 
							   'nisAntiMoneyLaGuidLine', 
							   'citizenCharter', 
							   'createdDate'
							  ];
		  
	}