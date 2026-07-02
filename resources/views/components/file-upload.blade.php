@props(['type', 'id', 'files' => collect()])

<div style="margin-top:16px;border-top:1px solid var(--c-border);padding-top:12px">
    <div style="font-size:12px;font-weight:600;color:var(--c-muted);margin-bottom:8px;display:flex;align-items:center;gap:6px"><x-icon name="file-text" :size="13" /> LAMPIRAN</div>

    @if($files->count())
    <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:12px">
        @foreach($files as $f)
        <div style="display:flex;align-items:center;gap:8px;padding:8px 10px;background:var(--c-bg);border-radius:8px;font-size:12px">
            @if($f->isImage())
                <a href="{{ $f->url }}" target="_blank" style="flex-shrink:0">
                    <img src="{{ $f->url }}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;border:1px solid var(--c-border)">
                </a>
            @else
                <span style="flex-shrink:0;color:var(--c-muted)"><x-icon name="file-text" :size="20" /></span>
            @endif
            <div style="flex:1;min-width:0">
                <a href="{{ $f->url }}" target="_blank" style="font-weight:600;color:var(--c-text);text-decoration:none;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $f->file_name }}</a>
                <span style="color:var(--c-muted)">{{ number_format($f->file_size / 1024, 0) }} KB • {{ $f->created_at->diffForHumans() }}</span>
            </div>
            <form method="POST" action="{{ route('files.destroy', $f) }}" onsubmit="return confirm('Padam fail ini?')" style="flex-shrink:0">
                @csrf @method('DELETE')
                <button type="submit" style="background:none;border:none;cursor:pointer;color:var(--c-danger);font-size:14px;padding:4px" title="Padam">✕</button>
            </form>
        </div>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('files.store') }}" enctype="multipart/form-data" style="display:flex;gap:8px;align-items:flex-end">
        @csrf
        <input type="hidden" name="uploadable_type" value="{{ $type }}">
        <input type="hidden" name="uploadable_id" value="{{ $id }}">
        <div style="flex:1">
            <input type="file" name="file" class="form-control" style="padding:6px 10px;font-size:12px" required accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
        </div>
        <button type="submit" class="btn btn-sm btn-secondary"><x-icon name="plus" :size="13" /> Muat Naik</button>
    </form>
    <div style="font-size:10px;color:var(--c-muted);margin-top:4px">Maks 10MB. Format: gambar, PDF, Word, Excel</div>
</div>
