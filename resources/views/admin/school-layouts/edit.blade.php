@extends('layouts.app')

@push('styles')
<style>
    .canvas-container {
        position: relative;
        border: 2px solid #ddd;
        border-radius: 10px;
        background: #fff;
    }

    #drawing-canvas {
        position: absolute;
        top: 0;
        left: 0;
        z-index: 1;
        cursor: crosshair;
    }

    .classrooms-layer {
        position: absolute;
        top: 0;
        left: 0;
        z-index: 10;
        pointer-events: none;
    }

    .classroom-box {
        position: absolute;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px;
        border-radius: 8px;
        cursor: move;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        min-width: 100px;
        text-align: center;
        user-select: none;
        pointer-events: all;
        font-size: 0.85rem;
    }

    .classroom-box:hover {
        box-shadow: 0 6px 15px rgba(0,0,0,0.3);
        transform: scale(1.05);
    }

    .drawing-tools {
        background: white;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .tool-btn {
        padding: 8px 12px;
        margin: 3px;
        border: 2px solid #ddd;
        border-radius: 6px;
        background: white;
        cursor: pointer;
        transition: all 0.2s;
    }

    .tool-btn:hover, .tool-btn.active {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .classroom-item {
        padding: 10px;
        margin-bottom: 8px;
        background: #f8f9fa;
        border-radius: 6px;
        cursor: grab;
        border-left: 4px solid #667eea;
        transition: all 0.2s;
    }

    .classroom-item:hover {
        background: #e9ecef;
        transform: translateX(3px);
    }

    .classroom-item.in-canvas {
        opacity: 0.5;
        border-left-color: #28a745;
        background: #d4edda;
    }
</style>
@endpush

@section('content')
<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3>Denah: {{ $schoolLayout->name }}</h3>
            <p class="text-muted">Upload gambar atau gambar denah, lalu drag-drop kelas</p>
        </div>
        <a href="{{ route('school-layouts.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-lg-9">
        <!-- Upload Background -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-image"></i> Background Gambar</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('school-layouts.update', $schoolLayout) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="name" value="{{ $schoolLayout->name }}">
                    <input type="hidden" name="floor_number" value="{{ $schoolLayout->floor_number }}">
                    <input type="hidden" name="width" value="{{ $schoolLayout->width }}">
                    <input type="hidden" name="height" value="{{ $schoolLayout->height }}">
                    
                    <div class="row align-items-end">
                        <div class="col-md-8">
                            <label class="form-label">Upload Gambar Denah</label>
                            <input type="file" class="form-control" name="background_image" accept="image/*">
                            <small class="text-muted">JPG/PNG, max 5MB</small>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-upload"></i> Upload
                            </button>
                        </div>
                    </div>
                    
                    @if($schoolLayout->background_image)
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <div class="text-success">
                                <i class="bi bi-check-circle"></i> Gambar sudah ada
                            </div>
                            <button type="button" class="btn btn-sm btn-danger" id="delete-background">
                                <i class="bi bi-trash"></i> Hapus Background
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Drawing Tools -->
        <div class="drawing-tools">
            <h6 class="mb-2"><i class="bi bi-brush"></i> Drawing Tools</h6>
            <button type="button" class="tool-btn" id="tool-select"><i class="bi bi-cursor"></i> Select</button>
            <button type="button" class="tool-btn active" id="tool-rect"><i class="bi bi-square"></i> Rectangle</button>
            <button type="button" class="tool-btn" id="tool-line"><i class="bi bi-dash-lg"></i> Line</button>
            <button type="button" class="tool-btn" id="tool-text"><i class="bi bi-fonts"></i> Text</button>
            <button type="button" class="tool-btn" id="tool-eraser"><i class="bi bi-eraser"></i> Eraser</button>
            
            <label class="ms-3">Warna:</label>
            <input type="color" id="draw-color" value="#333333" class="form-control d-inline-block" style="width: 60px;">
            
            <label class="ms-2">Ketebalan:</label>
            <input type="range" id="line-width" min="1" max="10" value="2" class="form-range d-inline-block" style="width: 100px;">
            
            <button type="button" class="btn btn-sm btn-info ms-3" id="undo-drawing">
                <i class="bi bi-arrow-counterclockwise"></i> Undo
            </button>
            <button type="button" class="btn btn-sm btn-warning" id="clear-drawing">
                <i class="bi bi-trash"></i> Clear Drawing
            </button>
            <button type="button" class="btn btn-sm btn-danger ms-2" id="clear-all">
                <i class="bi bi-x-circle"></i> Clear All Canvas
            </button>
        </div>

        <!-- Canvas -->
        <div class="card">
            <div class="card-body">
                <div class="canvas-container" id="canvas-container" 
                     style="width: {{ $schoolLayout->width }}px; height: {{ $schoolLayout->height }}px;">
                    
                    <canvas id="drawing-canvas" 
                            width="{{ $schoolLayout->width }}" 
                            height="{{ $schoolLayout->height }}"
                            @if($schoolLayout->background_image) 
                            style="background-image: url('{{ asset($schoolLayout->background_image) }}'); background-size: contain; background-repeat: no-repeat;"
                            @endif>
                    </canvas>
                    
                    <div class="classrooms-layer" id="classrooms-layer" 
                         style="width: {{ $schoolLayout->width }}px; height: {{ $schoolLayout->height }}px;">
                        @foreach($classroomsInLayout as $classroom)
                            <div class="classroom-box" 
                                 data-classroom-id="{{ $classroom->id }}"
                                 style="left: {{ $classroom->position_x ?? 50 }}px; top: {{ $classroom->position_y ?? 50 }}px;">
                                <strong>{{ $classroom->name }}</strong><br>
                                <small>{{ $classroom->students->count() }} siswa</small>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-3">
                    <button type="button" class="btn btn-success btn-lg" id="save-all">
                        <i class="bi bi-save"></i> Simpan Semua
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.reload()">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Kelas -->
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="bi bi-door-open"></i> Daftar Kelas</h6>
            </div>
            <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                <p class="small text-muted">Drag kelas ke canvas →</p>
                @foreach($classrooms as $classroom)
                    <div class="classroom-item {{ $classroom->school_layout_id == $schoolLayout->id ? 'in-canvas' : '' }}" 
                         data-classroom-id="{{ $classroom->id }}"
                         data-classroom-name="{{ $classroom->name }}">
                        <strong>{{ $classroom->name }}</strong><br>
                        <small>{{ $classroom->students->count() }} siswa</small>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// === DRAWING ===
const canvas = document.getElementById('drawing-canvas');
const ctx = canvas.getContext('2d');
let drawings = [];
let isDrawing = false;
let startX, startY;
let currentTool = 'rect';

// Load existing drawings
@if($schoolLayout->grid_data)
try {
    drawings = JSON.parse('{!! addslashes($schoolLayout->grid_data) !!}');
    redrawCanvas();
} catch(e) {
    console.error('Failed to load drawings:', e);
}
@endif

function redrawCanvas() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawings.forEach(shape => {
        ctx.strokeStyle = shape.color || '#333';
        ctx.lineWidth = shape.lineWidth || 2;
        ctx.fillStyle = shape.color || '#333';

        if (shape.type === 'rect') {
            ctx.strokeRect(shape.x, shape.y, shape.width, shape.height);
        } else if (shape.type === 'line') {
            ctx.beginPath();
            ctx.moveTo(shape.x1, shape.y1);
            ctx.lineTo(shape.x2, shape.y2);
            ctx.stroke();
        } else if (shape.type === 'text') {
            ctx.font = '14px Arial';
            ctx.fillText(shape.text, shape.x, shape.y);
        }
    });
}

document.querySelectorAll('.tool-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.tool-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        currentTool = this.id.replace('tool-', '');
    });
});

canvas.addEventListener('mousedown', e => {
    const rect = canvas.getBoundingClientRect();
    startX = e.clientX - rect.left;
    startY = e.clientY - rect.top;
    
    // Eraser mode - detect click on shape
    if (currentTool === 'eraser') {
        let clickedIndex = -1;
        
        // Check each shape to see if clicked
        for (let i = drawings.length - 1; i >= 0; i--) {
            const shape = drawings[i];
            
            if (shape.type === 'rect') {
                // Check if click is inside rectangle
                // Adjust for potential negative width/height from drawing
                const minX = Math.min(shape.x, shape.x + shape.width);
                const maxX = Math.max(shape.x, shape.x + shape.width);
                const minY = Math.min(shape.y, shape.y + shape.height);
                const maxY = Math.max(shape.y, shape.y + shape.height);

                if (startX >= minX && startX <= maxX &&
                    startY >= minY && startY <= maxY) {
                    clickedIndex = i;
                    break;
                }
            } else if (shape.type === 'line') {
                // Check if click is near line (with tolerance)
                const tolerance = 10;
                const dist = distanceToLine(startX, startY, shape.x1, shape.y1, shape.x2, shape.y2);
                if (dist < tolerance) {
                    clickedIndex = i;
                    break;
                }
            } else if (shape.type === 'text') {
                // Check if click is near text (simple box check)
                // Need to measure text width for accurate bounding box
                ctx.font = '14px Arial'; // Ensure font matches what was used for drawing
                const textWidth = ctx.measureText(shape.text).width;
                const textHeight = 14; // Approximate font height
                if (startX >= shape.x && startX <= shape.x + textWidth &&
                    startY >= shape.y - textHeight && startY <= shape.y) { // Text y is baseline
                    clickedIndex = i;
                    break;
                }
            }
        }
        
        if (clickedIndex !== -1) {
            drawings.splice(clickedIndex, 1);
            redrawCanvas();
        }
        return;
    }
    
    // Select mode - highlight shape
    if (currentTool === 'select') {
        // Could implement selection/move functionality here
        return;
    }
    
    // Text mode
    if (currentTool === 'text') {
        const text = prompt('Masukkan teks:');
        if (text) {
            drawings.push({
                type: 'text',
                x: startX,
                y: startY,
                text: text,
                color: document.getElementById('draw-color').value
            });
            redrawCanvas();
        }
        return;
    }
    
    isDrawing = true;
});

// Helper function to calculate distance from point to line
function distanceToLine(px, py, x1, y1, x2, y2) {
    const A = px - x1;
    const B = py - y1;
    const C = x2 - x1;
    const D = y2 - y1;

    const dot = A * C + B * D;
    const lenSq = C * C + D * D;
    let param = -1;

    if (lenSq != 0) param = dot / lenSq;

    let xx, yy;

    if (param < 0) {
        xx = x1;
        yy = y1;
    } else if (param > 1) {
        xx = x2;
        yy = y2;
    } else {
        xx = x1 + param * C;
        yy = y1 + param * D;
    }

    const dx = px - xx;
    const dy = py - yy;
    return Math.sqrt(dx * dx + dy * dy);
}

canvas.addEventListener('mousemove', e => {
    if (!isDrawing) return;
    
    const rect = canvas.getBoundingClientRect();
    const currX = e.clientX - rect.left;
    const currY = e.clientY - rect.top;
    
    redrawCanvas();
    
    ctx.strokeStyle = document.getElementById('draw-color').value;
    ctx.lineWidth = document.getElementById('line-width').value;
    
    if (currentTool === 'rect') {
        ctx.strokeRect(startX, startY, currX - startX, currY - startY);
    } else if (currentTool === 'line') {
        ctx.beginPath();
        ctx.moveTo(startX, startY);
        ctx.lineTo(currX, currY);
        ctx.stroke();
    }
});

canvas.addEventListener('mouseup', e => {
    if (!isDrawing) return;
    
    const rect = canvas.getBoundingClientRect();
    const endX = e.clientX - rect.left;
    const endY = e.clientY - rect.top;
    
    const color = document.getElementById('draw-color').value;
    const lineWidth = document.getElementById('line-width').value;
    
    if (currentTool === 'rect') {
        drawings.push({
            type: 'rect',
            x: startX,
            y: startY,
            width: endX - startX,
            height: endY - startY,
            color: color,
            lineWidth: lineWidth
        });
    } else if (currentTool === 'line') {
        drawings.push({
            type: 'line',
            x1: startX,
            y1: startY,
            x2: endX,
            y2: endY,
            color: color,
            lineWidth: lineWidth
        });
    }
    
    isDrawing = false;
    redrawCanvas();
});

document.getElementById('clear-drawing').addEventListener('click', () => {
    if (confirm('Hapus semua drawing?')) {
        drawings = [];
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }
});

document.getElementById('undo-drawing').addEventListener('click', () => {
    if (drawings.length > 0) {
        drawings.pop(); // Remove last drawing
        redrawCanvas();
    } else {
        alert('Tidak ada drawing untuk di-undo!');
    }
});

// === DRAG & DROP CLASSROOMS ===
const classroomsLayer = document.getElementById('classrooms-layer');
let draggedClassroomId = null;
let draggedClassroomName = null;

document.querySelectorAll('.classroom-item').forEach(item => {
    item.addEventListener('mousedown', function(e) {
        draggedClassroomId = this.getAttribute('data-classroom-id');
        draggedClassroomName = this.getAttribute('data-classroom-name');
        
        const onMouseMove = (moveEvent) => {
            // Visual feedback - could add a ghost element here
        };
        
        const onMouseUp = (upEvent) => {
            const containerRect = classroomsLayer.getBoundingClientRect();
            const x = upEvent.clientX - containerRect.left - 50;
            const y = upEvent.clientY - containerRect.top - 20;
            
            // Check if dropped on canvas
            if (x >= 0 && y >= 0 && x < containerRect.width && y < containerRect.height) {
                // Check if already exists
                const existing = document.querySelector(`.classroom-box[data-classroom-id="${draggedClassroomId}"]`);
                if (!existing) {
                    createClassroomBox(draggedClassroomId, draggedClassroomName, x, y);
                    item.classList.add('in-canvas');
                }
            }
            
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
        };
        
        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
    });
});

function createClassroomBox(id, name, x, y) {
    const box = document.createElement('div');
    box.className = 'classroom-box';
    box.setAttribute('data-classroom-id', id);
    box.style.left = Math.max(0, x) + 'px';
    box.style.top = Math.max(0, y) + 'px';
    box.innerHTML = `<strong>${name}</strong><br><small>Kelas</small>`;
    
    classroomsLayer.appendChild(box);
    makeClassroomDraggable(box);
}

function makeClassroomDraggable(box) {
    box.addEventListener('mousedown', function(e) {
        e.stopPropagation();
        
        const shiftX = e.clientX - box.getBoundingClientRect().left;
        const shiftY = e.clientY - box.getBoundingClientRect().top;
        
        const onMouseMove = (moveEvent) => {
            const containerRect = classroomsLayer.getBoundingClientRect();
            let newX = moveEvent.clientX - containerRect.left - shiftX;
            let newY = moveEvent.clientY - containerRect.top - shiftY;
            
            newX = Math.max(0, Math.min(newX, containerRect.width - box.offsetWidth));
            newY = Math.max(0, Math.min(newY, containerRect.height - box.offsetHeight));
            
            box.style.left = newX + 'px';
            box.style.top = newY + 'px';
        };
        
        const onMouseUp = () => {
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
        };
        
        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
    });
}

document.querySelectorAll('.classroom-box').forEach(makeClassroomDraggable);

// === SAVE ===
document.getElementById('save-all').addEventListener('click', function() {
    const positions = [];
    document.querySelectorAll('.classroom-box').forEach(box => {
        positions.push({
            classroom_id: parseInt(box.getAttribute('data-classroom-id')),
            x: parseInt(box.style.left),
            y: parseInt(box.style.top)
        });
    });
    
    console.log('Saving positions:', positions);
    console.log('Saving drawings:', drawings);
    
    // Save positions first
    fetch('{{ route("school-layouts.update-positions", $schoolLayout) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ positions: positions })
    })
    .then(res => {
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        return res.json();
    })
    .then(data => {
        console.log('Positions saved:', data);
        
        // Then save drawings - use form data instead of JSON
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('name', '{{ $schoolLayout->name }}');
        formData.append('floor_number', '{{ $schoolLayout->floor_number }}');
        formData.append('width', '{{ $schoolLayout->width }}');
        formData.append('height', '{{ $schoolLayout->height }}');
        formData.append('grid_data', JSON.stringify(drawings));
        
        return fetch('{{ route("school-layouts.update", $schoolLayout) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });
    })
    .then(res => {
        if (!res.ok) {
            return res.text().then(text => {
                console.error('Server response:', text);
                throw new Error(`HTTP error! status: ${res.status}`);
            });
        }
        // Check if response is JSON or HTML
        const contentType = res.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return res.json();
        } else {
            // If HTML (redirect), consider it success
            return { success: true };
        }
    })
    .then(data => {
        console.log('Drawings saved:', data);
        alert('✅ Berhasil disimpan!');
        window.location.reload();
    })
    .catch(err => {
        console.error('Error:', err);
        alert('❌ Gagal menyimpan: ' + err.message);
    });
});

// === DELETE BACKGROUND IMAGE ===
@if($schoolLayout->background_image)
document.getElementById('delete-background').addEventListener('click', function() {
    if (confirm('Hapus gambar background? Drawing dan posisi kelas tidak akan terpengaruh.')) {
        fetch('{{ route("school-layouts.update", $schoolLayout) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-HTTP-Method-Override': 'PUT'
            },
            body: JSON.stringify({
                name: '{{ $schoolLayout->name }}',
                floor_number: {{ $schoolLayout->floor_number }},
                width: {{ $schoolLayout->width }},
                height: {{ $schoolLayout->height }},
                background_image: null,
                _delete_bg: true
            })
        })
        .then(res => res.json())
        .then(data => {
            alert('✅ Background berhasil dihapus!');
            window.location.reload();
        })
        .catch(err => {
            console.error('Error:', err);
            alert('❌ Gagal menghapus background');
        });
    }
});
@endif

// === CLEAR ALL CANVAS ===
document.getElementById('clear-all').addEventListener('click', function() {
    if (confirm('⚠️ CLEAR ALL CANVAS?\n\nIni akan menghapus:\n- Semua drawing\n- Semua posisi kelas\n- Background image (jika ada)\n\nApakah Anda yakin?')) {
        // Clear drawings
        drawings = [];
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Remove all classroom boxes
        document.querySelectorAll('.classroom-box').forEach(box => box.remove());
        
        // Reset all classroom items in sidebar
        document.querySelectorAll('.classroom-item').forEach(item => {
            item.classList.remove('in-canvas');
        });
        
        // Delete background and clear all data from server
        fetch('{{ route("school-layouts.update", $schoolLayout) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-HTTP-Method-Override': 'PUT'
            },
            body: JSON.stringify({
                name: '{{ $schoolLayout->name }}',
                floor_number: {{ $schoolLayout->floor_number }},
                width: {{ $schoolLayout->width }},
                height: {{ $schoolLayout->height }},
                background_image: null,
                grid_data: null,
                _delete_bg: true
            })
        })
        .then(res => res.json())
        .then(data => {
            // Clear classroom positions
            return fetch('{{ route("school-layouts.update-positions", $schoolLayout) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ positions: [] })
            });
        })
        .then(res => res.json())
        .then(data => {
            alert('✅ Canvas berhasil dikosongkan!');
            window.location.reload();
        })
        .catch(err => {
            console.error('Error:', err);
            alert('❌ Gagal mengosongkan canvas');
        });
    }
});

</script>
@endpush
