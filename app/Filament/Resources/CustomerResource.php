<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use App\Models\PipelineStage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use function Laravel\Prompts\select;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Select::make('lead_source_id')
                    ->relationship('leadSource', 'name'),
                Forms\Components\Select::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple(),
                Forms\Components\Select::make('pipeline_stage_id')
                    ->relationship('pipelineStage', 'name', function ($query){
                        //its iportant to order by position to display the correct order
                        $query->orderBy('position', 'asc');
                    })
                    ->default(PipelineStage::where('is_default', true)->first()?->id),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Name')
                    ->formatStateUsing( function ($record){
                        $tagsList = view('customer.tagList',['tags' => $record->tags])->render();
                        return $record->first_name . " " . $record->last_name . " " . $tagsList;
                    })
                    ->html()
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('leadSource.name'),
                Tables\Columns\TextColumn::make('pipelineStage.name'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Move To Stage')
                    ->icon('heroicon-m-pencil-square')
                    ->form([
                        Forms\Components\Select::make('pipeline_stage_id')
                            ->label('Status')
                            ->options( PipelineStage::pluck('name', 'id')->toArray() )
                            ->default(function (Customer $record){
                                $currentPosition = $record->pipelineStage->position;
                                return PipelineStage::where('position', '>',$currentPosition)->first()?->id;
                            }),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                    ])
                    ->action(function(Customer $customer, array $data):void {
                        $customer->pipeline_stage_id = $data['pipeline_stage_id'];
                        $customer->save();

                        $customer->pipelineStageLogs()->create([
                            'pipeline_stage_id' => $data['pipeline_stage_id'],
                            'notes' => $data['notes'],
                            'user' => auth()->id()
                        ]);

                        Notification::make()
                            ->title('Customer Notification Pipeline Updated')
                            ->success()
                            ->send();
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
