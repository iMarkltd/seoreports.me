<?PHP

include_once('semrush_api.php');


$s = new SemRush($_REQUEST['domain_name']);

//echo "Main Report: ";
//print_r($s->getMainReport());

//you can also use the SEMRush PHP API to call different types of reports not listed
//print_r($s->callReport("domain_rank", array("export_columns"=>"Dn,Rk,Or,Ot,Oc,Ad,At,Ac")));


//echo "getMainKeywordReport: ";
//$data	=	$s->getMainKeywordReport("Seo");

//echo "getMainKeywordReport: ";
//print_r($s->getDomainHistory()); exit;

//echo "getOrganicKeywordsReport: ";
//print_r($s->getOrganicKeywordsReport());
$data	=	($s->getOrganicKeywordsReport());

//echo "getAdwordsKeywordReport: ";
//print_r($s->getAdwordsKeywordReport());

//echo "getOrganicURLReport";
//print_r($s->getOrganicURLReport());

//echo "getAdwordsURLReport";
//print_r($s->getAdwordsURLReport());


//echo 'getCompetitorsInOrganicSearchReport';
//print_r($s->getCompetitorsInOrganicSearchReport());

//echo 'getCompetitorsInAdwordsSearchReport';
//print_r($s->getCompetitorsInAdwordsSearchReport());

//echo 'getPotentialAdTrafficBuyersReport';
//print_r($s->getPotentialAdTrafficBuyersReport());

//echo 'getPotentialAdTrafficSellersReport';
//print_r($s->getPotentialAdTrafficSellersReport());

?>

		<h1>Organic Keywords Report </h3>
		<table id="example" class="table table-striped table-bordered" width="100%" cellspacing="0">        <thead>
            <tr>
                <th>Keywords</th>
                <th>Position</th>
                <th>Position Difference</th>
                <th>Traffic Percentage</th>
                <th>Costs Percentage</th>
                <th>Number of Results</th>
                <th>CPC(USD)</th>
                <th>Avrage Vol</th>
                <th>URL</th>
            </tr>
        </thead>
        <tbody>
			<?php foreach($data as $record) { ?>
            <tr>
                <td><?php echo $record['keyword']?></td>
                <td><?php echo $record['pos']?></td>
                <td><?php echo $record['position_difference']?></td>
                <td><?php echo $record['traffic_percent']?></td>
                <td><?php echo $record['costs_percent']?></td>
                <td><?php echo $record['number_of_results']?></td>
                <td><?php echo $record['cpc']?></td>
                <td><?php echo $record['average_vol.']?></td>
                <td><?php echo $record['url']?></td>
            </tr>
			<?php } ?>
        </tbody>
    </table>
                       		