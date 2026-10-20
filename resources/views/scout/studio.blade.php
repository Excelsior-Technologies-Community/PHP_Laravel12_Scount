@extends('layout.app')

@section('content')

<!-- Header Banner -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
            <span class="badge bg-primary bg-gradient p-2 rounded-3 shadow-sm"><i class="bi bi-cpu-fill"></i></span>
            Scout Index Management & Sync Studio
        </h3>
        <p class="text-muted small mb-0">Inspect search engine telemetry, trigger index imports, flush stale records, and manage searchable models.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <form method="POST" action="{{ route('scout.sync.all') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary rounded-pill px-3 shadow-sm">
                <i class="bi bi-arrow-repeat me-1"></i> Sync All Indexes
            </button>
        </form>
        <a href="{{ url('/') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-search me-1"></i> Live Explorer
        </a>
    </div>
</div>

<!-- Telemetry & Health Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3 fs-3">
                    <i class="bi bi-hdd-network-fill"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase fw-bold">Search Driver</div>
                    <div class="fs-4 fw-bold text-dark text-capitalize">{{ $driver }}</div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill small">Active Engine</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-3 bg-info bg-opacity-10 text-info p-3 fs-3">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase fw-bold">Queue Sync</div>
                    <div class="fs-4 fw-bold text-dark">{{ $queueSync ? 'Enabled' : 'Sync (Direct)' }}</div>
                    <span class="text-muted small">{{ $queueSync ? 'Background Worker' : 'Real-time On Save' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-3 bg-warning bg-opacity-10 text-warning p-3 fs-3">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase fw-bold">Total Queries Tracked</div>
                    <div class="fs-4 fw-bold text-dark">{{ number_format($totalSearches) }}</div>
                    <span class="text-muted small">{{ $totalKeywords }} Unique terms</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-3 bg-danger bg-opacity-10 text-danger p-3 fs-3">
                    <i class="bi bi-question-circle-fill"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase fw-bold">Zero-Result Queries</div>
                    <div class="fs-4 fw-bold text-dark">{{ number_format($zeroResultCount) }}</div>
                    <span class="text-muted small">Auto Typo-Tolerant</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Searchable Models Table -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-layers-fill text-primary me-2"></i>Registered Searchable Models</h5>
            <small class="text-muted">Models utilizing Laravel Scout's <code>Searchable</code> trait with custom indexing arrays</small>
        </div>
        <span class="badge bg-secondary rounded-pill px-3 py-2">{{ count($models) }} Models Active</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Model & Target Table</th>
                    <th>Searchable Attributes</th>
                    <th>Total Indexed Records</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Index Operations</th>
                </tr>
            </thead>
            <tbody>
                @foreach($models as $model)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-primary text-white p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                    <i class="bi bi-file-earmark-code"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $model['name'] }}</div>
                                    <code class="text-muted small">{{ $model['class'] }}</code>
                                    <div class="text-muted small">Table: <code>{{ $model['table'] }}</code></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($model['searchable_fields'] as $field)
                                    <span class="badge bg-light text-dark border rounded-pill">{{ $field }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            <span class="fs-6 fw-bold text-dark">{{ number_format($model['count']) }}</span>
                            <span class="text-muted small">rows</span>
                        </td>
                        <td>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                                <i class="bi bi-check-circle-fill me-1"></i>Searchable
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm">
                                <form method="POST" action="{{ route('scout.import') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="model" value="{{ $model['class'] }}">
                                    <button type="submit" class="btn btn-sm btn-outline-primary rounded-start-pill px-3" title="Import records into Scout search index">
                                        <i class="bi bi-arrow-repeat me-1"></i> Import Index
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('scout.flush') }}" class="d-inline" onsubmit="return confirm('Flush search index for {{ $model['name'] }}?');">
                                    @csrf
                                    <input type="hidden" name="model" value="{{ $model['class'] }}">
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-end-pill px-3" title="Flush all indexed records for this model">
                                        <i class="bi bi-trash3 me-1"></i> Flush
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Architecture & Setup Guide -->
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-sliders text-primary me-2"></i>Scout Driver Switching</h5>
                <p class="text-muted small">You can swap the search backend at any time via <code>.env</code> without altering query syntax:</p>

                <div class="p-3 bg-dark text-white rounded-3 font-monospace small mb-3">
                    <span class="text-secondary"># Current active driver</span><br>
                    <span class="text-info">SCOUT_DRIVER</span>=<span class="text-warning">{{ $driver }}</span><br><br>
                    <span class="text-secondary"># For Algolia engine</span><br>
                    <span class="text-muted"># SCOUT_DRIVER=algolia</span><br>
                    <span class="text-muted"># ALGOLIA_APP_ID=your_id</span><br>
                    <span class="text-muted"># ALGOLIA_SECRET=your_secret</span><br><br>
                    <span class="text-secondary"># For Meilisearch engine</span><br>
                    <span class="text-muted"># SCOUT_DRIVER=meilisearch</span><br>
                    <span class="text-muted"># MEILISEARCH_HOST=http://127.0.0.1:7700</span>
                </div>

                <div class="d-flex align-items-center gap-2 text-muted small">
                    <i class="bi bi-info-circle-fill text-primary"></i>
                    Database driver uses SQL <code>WHERE (title LIKE ? OR content LIKE ?)</code> with indexed columns and fuzzy fallback.
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-terminal text-primary me-2"></i>Artisan CLI Equivalents</h5>
                <p class="text-muted small">You can also execute index operations directly via terminal commands:</p>

                <ul class="list-group list-group-flush small">
                    <li class="list-group-item px-0 py-2 border-0">
                        <div class="fw-semibold text-dark">Re-index Posts:</div>
                        <code class="bg-light p-1 rounded d-block mt-1">php artisan scout:import "App\Models\Post"</code>
                    </li>
                    <li class="list-group-item px-0 py-2 border-0">
                        <div class="fw-semibold text-dark">Re-index Users:</div>
                        <code class="bg-light p-1 rounded d-block mt-1">php artisan scout:import "App\Models\User"</code>
                    </li>
                    <li class="list-group-item px-0 py-2 border-0">
                        <div class="fw-semibold text-dark">Flush Post Index:</div>
                        <code class="bg-light p-1 rounded d-block mt-1">php artisan scout:flush "App\Models\Post"</code>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
