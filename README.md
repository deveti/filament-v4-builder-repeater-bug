# 🐛 Bug: Repeater inside Builder Block can’t add or delete items

## Package
filament/filament

## Package Version
v4.0.6

## Laravel Version
v12.26.4

## Livewire Version
v3.6.4

## PHP Version
8.4.12

## Problem description
When using a **Builder** field and placing a **Repeater** inside a **Block**, adding or deleting repeater items does nothing. The UI briefly “flashes” as if the action is disallowed, but no items are added/removed. There are **no errors** in the browser console or Laravel logs.

This regression/bug prevents using repeaters within builder blocks.

## Expected Behavior
- Clicking **Add** should append a new repeater item within the block.
- Clicking **Delete** should remove the selected repeater item.
- State should update and persist normally.

## Steps to reproduce
1. Create a fresh Laravel app and install Filament v4 (Panel or just Forms).
2. Add a simple model (e.g., `Page`) with a JSON column `content`.
3. Create a Filament form where `content` is a Builder with a block that contains a Repeater.
4. In the form UI, try to **Add** an item to the Repeater → nothing happens (UI “flashes”).
5. Try to **Delete** an item (if you seeded one) → nothing happens (same UI flash).
6. Check browser console and Laravel logs → **no errors**.

## Minimal Reproducible Example

**Migration**
```php
// database/migrations/2025_09_02_000000_create_pages_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->json('content')->nullable(); // Builder state
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('pages');
    }
};
```

**Model**
```php
// app/Models/Page.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['title', 'content'];
    protected $casts = ['content' => 'array'];
}
```

**Filament Form (Panel Resource or standalone form)**
```php
// app/Filament/Resources/PageResource/Pages/CreatePage.php (excerpt)

use Filament\Forms;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Builder\Block;
use Filament\Resources\Pages\CreateRecord;

protected function getFormSchema(): array
{
    return [
        Forms\Components\TextInput::make('title')->required(),
        Builder::make('content')
            ->blocks([
                Block::make('faq')
                    ->schema([
                        Repeater::make('items')
                            ->schema([
                                TextInput::make('question')->required(),
                                TextInput::make('answer')->required(),
                            ])
                            ->defaultItems(0)
                            ->collapsed(),
                    ]),
            ]),
    ];
}
```

## Things I Tried (Workarounds)

- Moving the `Repeater` **outside** of the `Builder` → works normally.
- Using different field names / unique keys → no change.
- Toggling `->statePath()` custom paths on block/repeater → no change.
- Removing `->collapsed()` / `->defaultItems()` → no change.
- Disabling sortable features on Repeater → no change.

## Reproduction repository
https://github.com/mikeslinkman/export-notifcation-reproduction
