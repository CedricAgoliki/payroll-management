<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html" charset="utf-8">
	<style>
	    .page-break {
		page-break-after: always;
	    }
	    .paySheetTable table, th, td{
		border: 1px solid black;
		border-collapse: collapse;
		margin: auto;
		font-family: "sans-serif";
	    }
	    .paySheetTable th, td {
		text-align: center;
	    }
	    .element-title th { background-color: #aaa; }

	</style>
    </head>
    <body>
    <div>
    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="paySheetTable">
	<table>
	    <tr>
		<th colspan="5" style="border-bottom:1px solid transparent">BULLETIN DE SALAIRE</th>
	    </tr>
	    <tr>
		<td colspan="5" style="border-top:1px solid transparent; height: 30px;">
		    <?php echo e(monthAndYear($emp)); ?>

		</td>
	    </tr>

	    <tr class="element-title">
		<th colspan="1">MATRICULE</th>
		<th colspan="3">NOM ET PRENOMS</th>
		<th colspan="1">CATEGORIE</th>
	    </tr>
	    <tr>
		<td colspan="1"><?php echo e($emp->salaries[0]->registration_number); ?></td>
		<td colspan="3"><?php echo e($emp->last_name.' '.$emp->first_name); ?></td>
		<td colspan="1"><?php echo e($emp->salaries[0]->category); ?></td>
	    </tr>

	    <tr class="element-title">
		<th colspan="1">Date CDD/CDI/STAGE</th>
		<th colspan="3">NUMERO DE SECURITE SOCIALE</th>
		<th colspan="1">MODE DE PAYEMENT</th>
	    </tr>
	    <tr>
		<td colspan="1"><?php echo e(date("d / m / Y", strtotime($emp->contracts[0]->start))); ?></td>
		<td colspan="3"><?php echo e($emp->salaries[0]->social_security_number); ?></td>
		<td colspan="1"><?php echo e($emp->salaries[0]->payment_method); ?></td>
	    </tr>

	    <tr class="element-title">
		<th colspan="4">FONCTION</th>
		<th colspan="1">NUMERO DE COMPTE</th>
	    </tr>
	    <tr>
		<td colspan="4"><?php echo e($emp->salaries[0]->office); ?></td>
		<td colspan="1"><?php echo e($emp->salaries[0]->bank_account); ?></td>
	    </tr>


	    <tr class="element-title">
		<th>ELEMENT</th>
		<th>BASE DE CALCUL</th>
		<th>NB/TAUX</th>
		<th>RETENUES</th>
		<th>AVOIRS</th>
	    </tr>
	    <tr>
		<td style="text-align: left">SALAIRE DE BASE</td>
		<td><?php echo e((float) baseSalary($emp)); ?> </td>
		<td>30.00</td>
		<td></td>
		<td><?php echo e((float) baseSalary($emp)); ?></td>
	    </tr>
	    <?php $__currentLoopData = $emp->salaries[0]->elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	    <?php if($e->type === '+' && $e->pivot->allowed): ?>
	    <tr>
		<td style="text-align: left"><?php echo e(strtoupper($e->name)); ?></td>
		<td><?php echo e((float) $e->pivot->value); ?></td>
		<td>30.00</td>
		<td></td>
		<td>
	    <?php if($e->prorata): ?>
	    <?php echo e((float) $e->pivot->value  * ($emp->salaries[0]->workedDays / $emp->salaries[0]->openDays)); ?>

	    <?php else: ?>
	    <?php echo e((float) $e->pivot->value); ?>

	    <?php endif; ?>
		</td>
	    </tr>
	    <?php endif; ?>
	    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

	    <?php if($emp->salaries[0]->seniority_bonus): ?>
	    <tr>
		<td style="text-align: left">PRIME D'ANCIENNETÉ</td>
		<td><?php echo e((float) baseSalary($emp)); ?></td>
		<td><?php echo e(seniority_bonusRate($emp, $endDate)); ?></td>
		<td></td>
		<td><?php echo e(seniority_bonus($emp, $endDate)); ?></td>
	    </tr>
	    <?php endif; ?>

	    <?php $__currentLoopData = $emp->salaries[0]->elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	    <?php if($e->type === '-' && $e->pivot->allowed): ?>
	    <tr>
		<td style="text-align: left"><?php echo e(strtoupper($e->name)); ?></td>
		<td><?php echo e((float) $e->pivot->value); ?></td>
		<td>30.00</td>
		<td><?php echo e((float) $e->pivot->value); ?></td>
		<td></td>
	    </tr>
	    <?php endif; ?>
	    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	    <tr v-if="settings">
		  <td style="text-align: left">COTISATIONS CNSS</td>
		  <td>
		    <?php echo e(totalPrimesIrpp($emp)); ?>

		  </td>
		  <td><?php echo e($setting->employee_contribution_rate); ?> %</td>
		  <td>
		    <?php echo e(cnss($emp)); ?>

		  </td>
		  <td></td>
	    </tr>
	    <tr>
		  <td style="text-align: left">IMPOTS SUR LE REVENU</td>
		  <td><?php echo e(taxableWages($emp)); ?></td>
		  <td><?php echo e(irppRate($emp)); ?> %</td>
		  <td><?php echo e(irpp($emp)); ?></td>
		  <td></td>
	    </tr>
	    <tr>
		  <td style="text-align: left">TCS</td>
		  <td></td>
		  <td></td>
		  <td><?php echo e(tcs($emp)); ?></td>
		  <td></td>
	    </tr>
	    <tr>
		  <td style="text-align: left">AVANCE SUR SALAIRE</td>
		  <td><?php echo e((float) advances($emp->id, $startDate, $endDate)); ?></td>
		  <td></td>
		  <td><?php echo e((float) advances($emp->id, $startDate, $endDate)); ?></td>
		  <td></td>
	    </tr>
	    <tr>
                  <td style="text-align: left">CREDIT AU PERSONNEL</td>
		  <td><?php echo e((float) loans($emp->id, $startDate, $endDate)); ?></td>
                  <td></td>
		  <td><?php echo e((float) loans($emp->id, $startDate, $endDate)); ?></td>
		  <td></td>
	    </tr>
	    <tr>
		  <td colspan="3"><b>TOTAUX</b></td>
		  <td><b><?php echo e(totalDeductions($emp, $startDate, $endDate) + irpp($emp) + tcs($emp) + cnss($emp)); ?></b></td>
		  <td><b><?php echo e(totalPrimes($emp) + baseSalary($emp)); ?></b></td>
	    </tr>
	    <tr class="element-title">
		  <th>SALAIRE BRUT</th>
		  <th>RETENUES OBLIGATOIRES</th>
		  <th>SALAIRE NET</th>
		  <th>AUTRES RETENUES</th>
		  <th>NET A PAYER</th>
	    </tr>
	    <tr>
		  <td><b><?php echo e((float) grossSalary($emp)); ?></b></td>
		  <td><b><?php echo e((float) mandatoryDeductions($emp)); ?></b></td>
		  <td><b><?php echo e((float) netSalary($emp)); ?></b></td>
		  <td><b><?php echo e((float) otherDeductions($emp, $startDate, $endDate)); ?></b></td>
		  <td style="background: #aaa;"><b><?php echo e((float) netToPay($emp, $startDate, $endDate)); ?></b></td>
	    </tr>
      </table>
      <div style="margin:auto; width:68%;">
	<ul>solde congés : <?php echo e(leaveBalance($emp)); ?></ul>
	<ul>Personne à charge : <?php echo e($emp->dependants); ?></ul>
	<ul style="text-align:right;"><b>Le directeur executif</b></ul>
	<ul style="text-align:right;"><?php echo e($emp->salaries[0]->settings->director); ?></ul>
      </div>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    </body>
</html>
