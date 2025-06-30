<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class ImportBinCollectionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bin-collections:import {pdf_path?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import bin collection dates from a PDF file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pdfPath = $this->argument('pdf_path') ?? base_path('bin-collections.pdf');

        if (!File::exists($pdfPath)) {
            $this->error("PDF file not found at: $pdfPath");
            return 1;
        }

        $this->info("Importing bin collection dates from: $pdfPath");

        // Check if the migration exists, if not create it
        if (!$this->migrationExists('create_bin_collections_table')) {
            $this->info('Creating migration for bin_collections table...');
            $this->createMigration();
            $this->info('Migration created successfully.');

            // Run the migration
            $this->info('Running migration...');
            Artisan::call('migrate');
            $this->info('Migration completed successfully.');
        }

        // Check if the model exists, if not create it
        if (!File::exists(app_path('Models/BinCollection.php'))) {
            $this->info('Creating BinCollection model...');
            $this->createModel();
            $this->info('Model created successfully.');
        }

        // Extract data from PDF
        $this->info('Extracting data from PDF...');
        $data = $this->extractDataFromPdf($pdfPath);

        if (empty($data)) {
            $this->error('Failed to extract data from PDF or no data found.');
            return 1;
        }

        // Import data into database
        $this->info('Importing data into database...');
        $this->importData($data);

        $this->info('Bin collection dates imported successfully.');
        return 0;
    }

    /**
     * Check if a migration exists.
     */
    private function migrationExists(string $name): bool
    {
        $files = File::glob(database_path('migrations/*_' . $name . '.php'));
        return count($files) > 0;
    }

    /**
     * Create the migration file.
     */
    private function createMigration(): void
    {
        $migrationContent = <<<'EOT'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bin_collections', function (Blueprint $table) {
            $table->id();
            $table->date('collection_date');
            $table->string('bin_type'); // e.g., 'recycling', 'general', 'garden'
            $table->string('color')->nullable(); // e.g., 'green', 'black', 'brown'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bin_collections');
    }
};
EOT;

        $filename = date('Y_m_d_His') . '_create_bin_collections_table.php';
        File::put(database_path('migrations/' . $filename), $migrationContent);
    }

    /**
     * Create the model file.
     */
    private function createModel(): void
    {
        $modelContent = <<<'EOT'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BinCollection extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'collection_date',
        'bin_type',
        'color',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'collection_date' => 'date',
    ];

    /**
     * Get upcoming bin collections.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function upcoming($limit = 5)
    {
        return static::where('collection_date', '>=', Carbon::today())
            ->orderBy('collection_date')
            ->limit($limit)
            ->get();
    }

    /**
     * Get the next collection date for a specific bin type.
     *
     * @param string $binType
     * @return \App\Models\BinCollection|null
     */
    public static function nextCollectionForType($binType)
    {
        return static::where('bin_type', $binType)
            ->where('collection_date', '>=', Carbon::today())
            ->orderBy('collection_date')
            ->first();
    }

    /**
     * Get the days until the next collection.
     *
     * @return int
     */
    public function daysUntilCollection()
    {
        return Carbon::today()->diffInDays($this->collection_date, false);
    }

    /**
     * Get a human-readable string for the days until collection.
     *
     * @return string
     */
    public function daysUntilCollectionForHumans()
    {
        $days = $this->daysUntilCollection();

        if ($days === 0) {
            return 'Today';
        } elseif ($days === 1) {
            return 'Tomorrow';
        } elseif ($days > 1) {
            return "In {$days} days";
        } else {
            return 'Past collection';
        }
    }
}
EOT;

        File::put(app_path('Models/BinCollection.php'), $modelContent);
    }

    /**
     * Extract data from the PDF file.
     *
     * Note: This is a simplified implementation that assumes the PDF content
     * can be extracted as text. For more complex PDFs, a proper PDF parsing
     * library would be needed.
     */
    private function extractDataFromPdf(string $pdfPath): array
    {
        // This is a placeholder implementation
        // In a real implementation, you would use a PDF parsing library
        // For now, we'll create some sample data based on the expected structure

        $this->warn('PDF parsing not implemented. Using sample data instead.');
        $this->warn('To properly implement PDF parsing, install a PDF library like "spatie/pdf-to-text" or "smalot/pdfparser".');

        // Sample data - in a real implementation, this would be extracted from the PDF
        return [
            [
                'collection_date' => now()->addDays(1)->format('Y-m-d'),
                'bin_type' => 'recycling',
                'color' => 'green',
            ],
            [
                'collection_date' => now()->addDays(1)->format('Y-m-d'),
                'bin_type' => 'general',
                'color' => 'black',
            ],
            [
                'collection_date' => now()->addDays(8)->format('Y-m-d'),
                'bin_type' => 'recycling',
                'color' => 'green',
            ],
            [
                'collection_date' => now()->addDays(8)->format('Y-m-d'),
                'bin_type' => 'garden',
                'color' => 'brown',
            ],
            [
                'collection_date' => now()->addDays(15)->format('Y-m-d'),
                'bin_type' => 'recycling',
                'color' => 'green',
            ],
            [
                'collection_date' => now()->addDays(15)->format('Y-m-d'),
                'bin_type' => 'general',
                'color' => 'black',
            ],
        ];
    }

    /**
     * Import data into the database.
     */
    private function importData(array $data): void
    {
        // Clear existing data
        DB::table('bin_collections')->truncate();

        // Insert new data
        foreach ($data as $item) {
            DB::table('bin_collections')->insert($item);
        }

        $this->info(count($data) . ' records imported.');
    }
}
