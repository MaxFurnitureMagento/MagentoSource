<?php
class Celebros_Conversionpro_IndexController extends Mage_Core_Controller_Front_Action
{
	public function GetCronJobsAction()
    {
		echo '--------------------------------------------------------------------------------<BR><BR>';
		echo '&nbsp;Now:       '.date('l jS \of F Y h:i:s A') ."<BR>";
		echo '--------------------------------------------------------------------------------<BR><BR>';
		
		$cronJobs = Mage::getModel('cron/schedule')->getCollection();
    	
    	echo "Number of Cron jobs: ".count($cronJobs)."<BR><BR>";
    	
    	$runningJobsNum=$pendingJobsNum=$successJobsNum=$missedJobsNum=$errorJobsNum=0;
    	    	
    	foreach ($cronJobs as $cronJob)
    	{
			switch ($cronJob->getStatus())
			{
				case 'running':
					$runningJobsNum++;
					break;
				case 'pending':
					$pendingJobsNum++;
					break;
				case 'success':
					$successJobsNum++;
					break;
				case 'missed':
					$missedJobsNum++;
					break;
				case 'error':
					$errorJobsNum++;
					break;
			}
				
			if ($cronJob->getJobCode()=='conversionpro_export')
			{
				echo "JobCode: ".$cronJob->getJobCode()."<BR>";
				echo "CreatedAt: ".$cronJob->getCreatedAt()."<BR>";
				echo "ScheduledAt: ".$cronJob->getScheduledAt()."<BR>";
				echo "getMessages: ".$cronJob->getMessages()."<BR>";
				echo "getStatus: ".$cronJob->getStatus()."<BR><BR>";
			}
			
		}
		
		echo "Number of running jobs: ".$runningJobsNum."<BR>";
		echo "Number of pending jobs: ".$pendingJobsNum."<BR>";
		echo "Number of success jobs: ".$successJobsNum."<BR>";
		echo "Number of missed jobs: ".$missedJobsNum."<BR>";
		echo "Number of error jobs: ".$errorJobsNum."<BR>";
	}
}
