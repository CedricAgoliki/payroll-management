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
    <div class="paySheetTable">
	<table>
	    <tr>
		<th colspan="5" style="border-bottom:1px solid transparent">BULLETIN DE SALAIRE</th>
	    </tr>
	    <tr>
		<td colspan="5" style="border-top:1px solid transparent; height: 30px;">
		    {{ monthAndYear($emp) }}
		</td>
	    </tr>

	    <tr class="element-title">
		<th colspan="1">MATRICULE</th>
		<th colspan="3">NOM ET PRENOMS</th>
		<th colspan="1">CATEGORIE</th>
	    </tr>
	    <tr>
		<td colspan="1">{{ $emp->salaries[0]->registration_number }}</td>
		<td colspan="3">{{ $emp->last_name.' '.$emp->first_name }}</td>
		<td colspan="1">{{ $emp->salaries[0]->category }}</td>
	    </tr>

	    <tr class="element-title">
		<th colspan="1">Date CDD/CDI/STAGE</th>
		<th colspan="3">NUMERO DE SECURITE SOCIALE</th>
		<th colspan="1">MODE DE PAYEMENT</th>
	    </tr>
	    <tr>
		<td colspan="1">{{ date("d / m / Y", strtotime($emp->contracts[0]->start)) }}</td>
		<td colspan="3">{{ $emp->salaries[0]->social_security_number }}</td>
		<td colspan="1">{{ $emp->salaries[0]->payment_method }}</td>
	    </tr>

	    <tr class="element-title">
		<th colspan="4">FONCTION</th>
		<th colspan="1">NUMERO DE COMPTE</th>
	    </tr>
	    <tr>
		<td colspan="4">{{ $emp->salaries[0]->office }}</td>
		<td colspan="1">{{ $emp->salaries[0]->bank_account }}</td>
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
		<td>{{ (float) baseSalary($emp) }} </td>
		<td>30.00</td>
		<td></td>
		<td>{{ (float) baseSalary($emp) }}</td>
	    </tr>
	    @foreach ($emp->salaries[0]->elements as $e)
	    @if ($e->type === '+' && $e->pivot->allowed)
	    <tr>
		<td style="text-align: left">{{ strtoupper($e->name) }}</td>
		<td>{{ (float) $e->pivot->value }}</td>
		<td>30.00</td>
		<td></td>
		<td>
	    @if ($e->prorata)
	    {{ (float) $e->pivot->value  * ($emp->salaries[0]->workedDays / $emp->salaries[0]->openDays) }}
	    @else
	    {{ (float) $e->pivot->value }}
	    @endif
		</td>
	    </tr>
	    @endif
	    @endforeach

	    @if ($emp->salaries[0]->seniority_bonus)
	    <tr>
		<td style="text-align: left">PRIME D'ANCIENNETÉ</td>
		<td>{{ (float) baseSalary($emp) }}</td>
		<td>{{ seniority_bonusRate($emp, $endDate) }}</td>
		<td></td>
		<td>{{ seniority_bonus($emp, $endDate) }}</td>
	    </tr>
	    @endif

	    @foreach ($emp->salaries[0]->elements as $e)
	    @if ($e->type === '-' && $e->pivot->allowed)
	    <tr>
		<td style="text-align: left">{{ strtoupper($e->name) }}</td>
		<td>{{ (float) $e->pivot->value }}</td>
		<td>30.00</td>
		<td>{{ (float) $e->pivot->value }}</td>
		<td></td>
	    </tr>
	    @endif
	    @endforeach
	    <tr v-if="settings">
		  <td style="text-align: left">COTISATIONS CNSS</td>
		  <td>
		    {{ totalPrimesIrpp($emp) }}
		  </td>
		  <td>{{ $setting->employee_contribution_rate }} %</td>
		  <td>
		    {{ cnss($emp) }}
		  </td>
		  <td></td>
	    </tr>
	    <tr>
		  <td style="text-align: left">IMPOTS SUR LE REVENU</td>
		  <td>{{ taxableWages($emp) }}</td>
		  <td>{{ irppRate($emp) }} %</td>
		  <td>{{ irpp($emp) }}</td>
		  <td></td>
	    </tr>
	    <tr>
		  <td style="text-align: left">TCS</td>
		  <td></td>
		  <td></td>
		  <td>{{ tcs($emp) }}</td>
		  <td></td>
	    </tr>
	    <tr>
		  <td style="text-align: left">AVANCE SUR SALAIRE</td>
		  <td>{{ (float) advances($emp->id, $startDate, $endDate) }}</td>
		  <td></td>
		  <td>{{ (float) advances($emp->id, $startDate, $endDate) }}</td>
		  <td></td>
	    </tr>
	    <tr>
                  <td style="text-align: left">CREDIT AU PERSONNEL</td>
		  <td>{{ (float) loans($emp->id, $startDate, $endDate) }}</td>
                  <td></td>
		  <td>{{ (float) loans($emp->id, $startDate, $endDate) }}</td>
		  <td></td>
	    </tr>
	    <tr>
		  <td colspan="3"><b>TOTAUX</b></td>
		  <td><b>{{ totalDeductions($emp, $startDate, $endDate) + irpp($emp) + tcs($emp) + cnss($emp) }}</b></td>
		  <td><b>{{ totalPrimes($emp) + baseSalary($emp) }}</b></td>
	    </tr>
	    <tr class="element-title">
		  <th>SALAIRE BRUT</th>
		  <th>RETENUES OBLIGATOIRES</th>
		  <th>SALAIRE NET</th>
		  <th>AUTRES RETENUES</th>
		  <th>NET A PAYER</th>
	    </tr>
	    <tr>
		  <td><b>{{ (float) grossSalary($emp) }}</b></td>
		  <td><b>{{ (float) mandatoryDeductions($emp) }}</b></td>
		  <td><b>{{ (float) netSalary($emp) }}</b></td>
		  <td><b>{{ (float) otherDeductions($emp, $startDate, $endDate) }}</b></td>
		  <td style="background: #aaa;"><b>{{ (float) netToPay($emp, $startDate, $endDate) }}</b></td>
	    </tr>
      </table>
      <div style="margin:auto; width:68%;">
	<ul>solde congés : {{ leaveBalance($emp) }}</ul>
	<ul>Personne à charge : {{$emp->dependants}}</ul>
	<ul style="text-align:right;"><b>Le directeur executif</b></ul>
	<ul style="text-align:right;">{{ $emp->salaries[0]->settings->director }}</ul>
      </div>
    </div>
    </body>
</html>
