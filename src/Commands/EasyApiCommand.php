<?php

namespace Rdcstarr\EasyApi\Commands;

use Exception;
use Illuminate\Console\Command;
use Rdcstarr\EasyApi\Facades\EasyApi;
use Rdcstarr\EasyApi\Models\Api;
use Throwable;
use function Laravel\Prompts\confirm;

class EasyApiCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'easyapi
		{action : Action to perform (generate, delete, list, reveal)}
		{--key= : API key to delete (required for delete action)}
		{--id= : API id to reveal (required for reveal action)}
	';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$action = $this->argument('action');

		return match ($action)
		{
			'generate' => $this->generateApiKey(),
			'delete' => $this->deleteApiKey(),
			'list' => $this->listApiKeys(),
			'reveal' => $this->revealApiKey(),
			default => $this->invalidAction($action),
		};
	}

	/**
	 * Generate a new API key.
	 */
	private function generateApiKey(): void
	{
		try
		{
			$apiKey = EasyApi::createKey();

			$this->components->info('API Key generated successfully!');

			$this->line("  <fg=green>🔑 API Key:</fg=green> [<fg=yellow>{$apiKey->key}</fg=yellow>]");
			$this->newLine();
			$this->components->warn('Keep this key secure!');
			return;
		}
		catch (Throwable $e)
		{
			$this->error("Failed to generate API key: {$e->getMessage()}");
			return;
		}
	}

	/**
	 * Delete an existing API key.
	 */
	private function deleteApiKey(): void
	{
		$key = $this->option('key');

		if (!$key)
		{
			$this->components->error('API key is required for delete action');
			$this->line('  Usage: [php artisan api:key delete <api-key>]');
			$this->newLine();
			return;
		}

		if (!confirm("This action will not be reversible. Are you sure you want to continue?"))
		{
			$this->warn('🚫 API key deletion was canceled.');
			return;
		}

		try
		{
			EasyApi::deleteKey($key);

			$this->components->success('API key deleted successfully');
			return;
		}
		catch (Throwable $e)
		{
			$this->components->error("Failed to delete API key: {$e->getMessage()}");
			return;
		}
	}

	/**
	 * List all API keys.
	 */
	private function listApiKeys()
	{
		$apiKeys = Api::orderBy('access_count', 'desc')->get();

		if ($apiKeys->isEmpty())
		{
			$this->components->info('No API keys found');
			return;
		}

		$this->components->info('API Keys:');

		$headers = ['ID', 'Key', 'Access Count', 'Last accesed', 'Created'];
		$rows    = $apiKeys->map(fn($key) => [
			$key->id,
			$key->masked_key,
			number_format($key->access_count),
			$key->updated_at->format('Y-m-d H:i:s'),
			$key->created_at->format('Y-m-d H:i:s'),
		])->toArray();

		$this->table($headers, $rows);

		return;
	}

	/**
	 * Reveal full API key by ID.
	 */
	private function revealApiKey(): void
	{
		$id = $this->option('id');

		if (!$id)
		{
			$this->components->error('API id is required for reveal action');
			$this->line('  Usage: [php artisan api:key reveal <api-id>]');
			$this->newLine();
			return;
		}

		try
		{
			$api = Api::whereId($id)->first();

			if (!$api)
			{
				$this->components->error('API key not found');
				return;
			}

			$this->components->info('API Key found:');
			$this->line("  <fg=green>🔑 API Key:</fg=green> [<fg=yellow>{$api->key}</fg=yellow>]");
			$this->newLine();
			return;
		}
		catch (Exception $e)
		{
			$this->components->error("Failed to retrieve API key: {$e->getMessage()}");
			return;
		}
	}

	/**
	 * Handle invalid action.
	 */
	private function invalidAction(string $action)
	{
		$this->components->error("Invalid action: {$action}");

		$this->components->info('Available actions:');
		$this->line('    <fg=green>generate</fg=green>  Generate a new API key');
		$this->line('    <fg=green>delete</fg=green>    Delete an existing API key');
		$this->line('    <fg=green>list</fg=green>      List all API keys');
		$this->line('    <fg=green>reveal</fg=green>    Reveal full API key by ID');
		$this->newLine();

		$this->components->info('Examples:');
		$this->line('    <fg=yellow>php artisan easyapi generate</fg=yellow>');
		$this->line('    <fg=yellow>php artisan easyapi delete --key=eapi_abc123...</fg=yellow>');
		$this->line('    <fg=yellow>php artisan easyapi list</fg=yellow>');
		$this->line('    <fg=yellow>php artisan easyapi reveal --id=1</fg=yellow>');
		$this->newLine();

		return;
	}
}
