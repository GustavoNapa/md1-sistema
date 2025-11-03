<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class InscriptionSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		if (! Schema::hasTable('inscriptions')) {
			return;
		}

		// possíveis nomes para o campo "Pagamento no"
		$paymentCandidates = ['payment_no', 'payment_on', 'payment_at', 'pagamento_no', 'pagamento_em'];
		// possíveis nomes para o campo "Vendedor"
		$sellerCandidates = ['seller', 'vendedor', 'vendor', 'salesman'];

		$paymentCol = null;
		foreach ($paymentCandidates as $c) {
			if (Schema::hasColumn('inscriptions', $c)) {
				$paymentCol = $c;
				break;
			}
		}

		$sellerCol = null;
		foreach ($sellerCandidates as $c) {
			if (Schema::hasColumn('inscriptions', $c)) {
				$sellerCol = $c;
				break;
			}
		}

		// nada a fazer se nenhum dos campos existir
		if (is_null($paymentCol) && is_null($sellerCol)) {
			return;
		}

		$now = Carbon::now()->toDateTimeString();

		// se a tabela estiver vazia, insere alguns registros de exemplo
		$count = DB::table('inscriptions')->count();
		if ($count === 0) {
			$toInsert = [];
			for ($i = 1; $i <= 5; $i++) {
				$row = [
					'created_at' => $now,
					'updated_at' => $now,
				];
				if ($paymentCol) {
					$row[$paymentCol] = 'PIX-' . str_pad($i, 3, '0', STR_PAD_LEFT);
				}
				if ($sellerCol) {
					$row[$sellerCol] = 'Vendedor #' . $i;
				}
				$toInsert[] = $row;
			}
			DB::table('inscriptions')->insert($toInsert);
			return;
		}

		// atualiza registros existentes que tenham os campos nulos/vazios
		$rows = DB::table('inscriptions')->get();
		foreach ($rows as $r) {
			$updates = [];
			if ($paymentCol && (is_null($r->{$paymentCol}) || $r->{$paymentCol} === '')) {
				$idSuffix = isset($r->id) ? $r->id : random_int(1, 999);
				$updates[$paymentCol] = 'PIX-' . str_pad($idSuffix, 3, '0', STR_PAD_LEFT);
			}
			if ($sellerCol && (is_null($r->{$sellerCol}) || $r->{$sellerCol} === '')) {
				$idSuffix = isset($r->id) ? $r->id : random_int(1, 999);
				$updates[$sellerCol] = 'Vendedor #' . $idSuffix;
			}
			if (! empty($updates)) {
				$updates['updated_at'] = $now;
				DB::table('inscriptions')->where('id', $r->id)->update($updates);
			}
		}
	}
}
