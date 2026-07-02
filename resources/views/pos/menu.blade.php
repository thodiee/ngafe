@extends('layouts.app')
@section('content')
<div class="flex flex-col lg:flex-row gap-6 h-[85vh] animate-fade-in-up">
    <!-- Menu Section -->
    <div class="w-full lg:w-2/3 flex flex-col h-full">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-3xl font-bold text-[#2c2520] dark:text-white"><i class="fa-solid fa-utensils text-brand-500 mr-2"></i> POS Menu</h2>
            @if(isset($activeSession))
            <a href="{{ route('pos.shift.close') }}" class="bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded-xl font-bold transition duration-150 text-sm flex items-center shadow-lg hover:shadow-red-600/20 active:scale-95">
                <i class="fa-solid fa-lock mr-2"></i> Tutup Shift
            </a>
            @endif
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            <div class="bg-white/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 p-3 rounded-xl flex items-center gap-3 transition">
                <div class="w-10 h-10 rounded-full bg-brand-500/10 text-brand-500 flex items-center justify-center text-lg"><i class="fa-solid fa-box-open"></i></div>
                <div>
                    <div class="text-[10px] uppercase font-bold tracking-wider text-slate-500 dark:text-slate-400">Menu Aktif</div>
                    <div id="statMenuAktif" class="text-lg font-bold text-[#2c2520] dark:text-white leading-tight">{{ $activeProductsCount ?? 0 }}</div>
                </div>
            </div>
            <div class="bg-white/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 p-3 rounded-xl flex items-center gap-3 transition">
                <div class="w-10 h-10 rounded-full bg-orange-500/10 text-orange-500 flex items-center justify-center text-lg"><i class="fa-solid fa-fire-burner"></i></div>
                <div>
                    <div class="text-[10px] uppercase font-bold tracking-wider text-slate-500 dark:text-slate-400">Pesanan Berjalan</div>
                    <div id="statPesananBerjalan" class="text-lg font-bold text-[#2c2520] dark:text-white leading-tight">{{ $activeOrdersCount ?? 0 }}</div>
                </div>
            </div>
            <div class="bg-white/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 p-3 rounded-xl flex items-center gap-3 transition">
                <div class="w-10 h-10 rounded-full bg-blue-500/10 text-blue-500 flex items-center justify-center text-lg"><i class="fa-solid fa-check-double"></i></div>
                <div>
                    <div class="text-[10px] uppercase font-bold tracking-wider text-slate-500 dark:text-slate-400">Pesanan Selesai</div>
                    <div id="statPesananSelesai" class="text-lg font-bold text-[#2c2520] dark:text-white leading-tight">{{ $completedOrdersCount ?? 0 }}</div>
                </div>
            </div>
            <div class="bg-white/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 p-3 rounded-xl flex items-center gap-3 transition">
                <div class="w-10 h-10 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg"><i class="fa-solid fa-money-bill-wave"></i></div>
                <div>
                    <div class="text-[10px] uppercase font-bold tracking-wider text-slate-500 dark:text-slate-400">Penjualan Shift</div>
                    <div id="statPenjualanShift" class="text-lg font-bold text-emerald-600 dark:text-emerald-400 leading-tight">Rp {{ number_format($shiftSales ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="flex flex-col gap-3 mb-6">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="searchInput" placeholder="Cari menu apa hari ini?" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-3 pl-12 pr-4 text-[#2c2520] dark:text-white focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition shadow-md" onkeyup="filterProducts()">
            </div>
            
            <div class="flex overflow-x-auto pb-2 gap-2 hide-scroll">
                <button onclick="setCategoryFilter('all')" id="cat-all" class="category-btn whitespace-nowrap bg-brand-600 text-white px-5 py-2 rounded-full font-semibold transition shadow-md duration-150">Semua Menu</button>
                @foreach($categories as $cat)
                <button onclick="setCategoryFilter('{{ $cat->id }}')" id="cat-{{ $cat->id }}" class="category-btn whitespace-nowrap bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 px-5 py-2 rounded-full font-semibold transition border border-slate-200 dark:border-slate-700 duration-150">
                    {{ $cat->name }}
                </button>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 overflow-y-auto pr-2 pb-10" id="productGrid">
            @foreach($products as $product)
            <div class="product-card glass-luxury p-4 rounded-2xl cursor-pointer hover:border-brand-500 dark:hover:border-brand-400 hover:scale-[1.02] active:scale-[0.98] transition duration-200 group relative" 
                 data-category="{{ $product->category_id }}" 
                 data-name="{{ strtolower($product->name) }}"
                 onclick="handleProductClick({{ $product->toJson() }})">
                @if($product->is_package)
                    <div class="absolute top-0 right-0 bg-amber-500 text-amber-950 px-2 py-1 rounded-bl-xl rounded-tr-2xl font-bold text-[10px] z-10"><i class="fa-solid fa-box-open"></i> PAKET</div>
                @endif
                <div class="aspect-video bg-slate-100 dark:bg-slate-800 rounded-xl mb-3 flex items-center justify-center overflow-hidden transition">
                    @if($product->image)
                        <img src="{{ $product->image }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    @else
                        <i class="fa-solid fa-burger text-4xl text-slate-400 dark:text-slate-600"></i>
                    @endif
                </div>
                <h3 class="font-semibold text-lg text-[#2c2520] dark:text-white mb-1 leading-tight">{{ $product->name }}</h3>
                <p class="text-brand-600 dark:text-brand-400 font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                @if($product->variants->count() > 0)
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-2 bg-slate-200/50 dark:bg-slate-800 inline-block px-2 py-0.5 rounded transition">Ada {{ $product->variants->count() }} Pilihan Varian</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    
    <!-- Cart Section -->
    <div class="w-full lg:w-1/3 glass-luxury p-6 rounded-3xl flex flex-col border-t-4 border-t-brand-500 dark:border-t-brand-400 shadow-2xl relative">
        <!-- Tabs Header -->
        <div class="flex border-b border-slate-200 dark:border-slate-700 mb-4">
            <button type="button" onclick="switchSidebarTab('pos')" id="tabBtnPos" class="flex-1 pb-2 font-bold text-center border-b-2 border-brand-500 text-brand-600 dark:text-brand-400 text-sm flex items-center justify-center gap-1.5 transition focus:outline-none">
                <i class="fa-solid fa-cart-shopping"></i> Order POS
            </button>
            <button type="button" onclick="switchSidebarTab('table')" id="tabBtnTable" class="flex-1 pb-2 font-bold text-center border-b-2 border-transparent text-slate-500 hover:text-[#2c2520] dark:hover:text-white text-sm flex items-center justify-center gap-1.5 transition relative focus:outline-none">
                <i class="fa-solid fa-bell-concierge"></i> Pesanan Meja
                <span id="badgeTableOrdersContainer" class="{{ $unpaidOrders->count() > 0 ? '' : 'hidden' }}">
                    <span id="badgeTableOrders" class="absolute -top-1.5 right-2 bg-red-500 text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center border border-slate-50 dark:border-slate-900 animate-bounce">
                        {{ $unpaidOrders->count() }}
                    </span>
                </span>
            </button>
        </div>
        
        <form action="{{ route('pos.order.store') }}" method="POST" id="orderForm" class="flex flex-col h-full">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm text-slate-500 dark:text-slate-400 mb-1">Select Table</label>
                <select name="table_id" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg p-2 text-[#2c2520] dark:text-white transition" required>
                    <option value="">-- Choose Table --</option>
                    @foreach($tables as $table)
                        @php
                            $reservationStr = '';
                            if($table->reservations->count() > 0) {
                                $r = $table->reservations->first();
                                $reservationStr = " (RSV: {$r->customer_name} @ {$r->reservation_time->format('H:i')})";
                            }
                        @endphp
                        <option value="{{ $table->id }}" {{ $table->reservations->count() > 0 ? 'class=text-yellow-500' : '' }}>
                            Table {{ $table->table_number }} [{{ strtoupper($table->area->name ?? 'TANPA AREA') }}] {{ $reservationStr }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm text-slate-500 dark:text-slate-400 mb-1">Order Type</label>
                <div class="flex gap-2">
                    <label class="flex-1 text-center cursor-pointer">
                        <input type="radio" name="order_type" value="dine_in" class="peer hidden" checked>
                        <div class="peer-checked:bg-brand-600 peer-checked:text-white bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 p-2 rounded-lg transition duration-150"><i class="fa-solid fa-bell-concierge mr-1"></i> Dine In</div>
                    </label>
                    <label class="flex-1 text-center cursor-pointer">
                        <input type="radio" name="order_type" value="takeaway" class="peer hidden">
                        <div class="peer-checked:bg-brand-600 peer-checked:text-white bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 p-2 rounded-lg transition duration-150"><i class="fa-solid fa-bag-shopping mr-1"></i> Takeaway</div>
                    </label>
                </div>
            </div>

            <div class="mb-2">
                <label class="block text-sm text-slate-500 dark:text-slate-400 mb-1">Pelanggan (Member) - Opsional</label>
                <select name="customer_id" id="customer_id" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg p-2 text-[#2c2520] dark:text-white transition" onchange="handleCustomerChange()">
                    <option value="" data-points="0" data-is-birthday="false">-- Bukan Member --</option>
                    @foreach($customers as $cust)
                        @php
                            $isBirthday = $cust->dob && \Carbon\Carbon::parse($cust->dob)->isBirthday() ? 'true' : 'false';
                        @endphp
                        <option value="{{ $cust->id }}" data-points="{{ $cust->points }}" data-is-birthday="{{ $isBirthday }}">{{ $cust->name }} ({{ $cust->phone }})</option>
                    @endforeach
                </select>
            </div>
            
            <div id="bdayAlert" class="hidden mb-4 p-3 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/50 rounded-lg animate-pulse">
                <div class="font-bold text-amber-600 dark:text-amber-400 flex items-center mb-1">
                    <i class="fa-solid fa-cake-candles mr-2"></i> Pelanggan Ulang Tahun!
                </div>
                <div class="text-xs text-amber-800 dark:text-amber-200">{{ $bday_promo_message }}</div>
            </div>
            
            <div id="pointSection" class="hidden mb-4 p-3 bg-slate-50 dark:bg-slate-850 rounded-lg border border-slate-200 dark:border-brand-500/30 transition">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-slate-500 dark:text-slate-400">Poin Tersedia:</span>
                    <span id="availablePoints" class="font-bold text-emerald-600 dark:text-emerald-400">0</span>
                </div>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="use_points" id="use_points" class="w-4 h-4 text-brand-600 bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 rounded focus:ring-brand-600 focus:ring-2" onchange="updateCartUI()">
                    <span class="text-sm text-slate-700 dark:text-slate-300">Gunakan semua poin untuk diskon?</span>
                </label>
                <div id="discountInfo" class="hidden text-xs text-brand-600 dark:text-brand-400 mt-2 text-right"></div>
            </div>
            
            <div id="cartItems" class="flex-grow overflow-y-auto mb-4 space-y-2 border-t border-slate-200 dark:border-slate-700 pt-4">
                <div class="text-center text-slate-400 dark:text-slate-500 mt-10" id="emptyCart">Cart is empty</div>
            </div>
            
            <div class="border-t border-slate-200 dark:border-slate-700 pt-4 mb-4">
                <div id="discountRow" class="flex justify-between items-center text-sm text-emerald-600 dark:text-emerald-400 hidden mb-1">
                    <span>Diskon Poin</span>
                    <span id="cartDiscount">- Rp 0</span>
                </div>
                <div class="flex justify-between items-center text-xl font-bold">
                    <span>Total Bayar</span>
                    <span id="cartTotal" class="text-brand-600 dark:text-brand-400">Rp 0</span>
                </div>
                <div id="earnedRow" class="text-xs text-slate-500 text-right mt-1 hidden">
                    + <span id="cartEarned">0</span> Poin (Member)
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm text-slate-500 dark:text-slate-400 mb-2">Payment Method</label>
                <select name="payment_method" id="payment_method" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg p-2 text-[#2c2520] dark:text-white transition" required>
                    <option value="cash">Cash</option>
                    <option value="qris_gateway">QRIS Gateway</option>
                </select>
            </div>
            
            <button type="button" onclick="showConfirmationModal()" class="w-full bg-gradient-to-r from-brand-600 to-amber-600 hover:from-brand-500 hover:to-amber-500 text-white font-bold py-4 rounded-xl shadow-lg transition duration-150 active:scale-95 text-lg hover:shadow-brand-500/20" id="checkoutBtn" disabled>
                Proses Pesanan
            </button>
        </form>

        <!-- Unpaid Self-Service Table Orders List -->
        <div id="tableOrdersContainer" class="flex-grow overflow-y-auto space-y-3 hidden pr-1">
            @include('pos.partials.unpaid_orders')
        </div>
    </div>
</div>

<!-- Order Confirmation Modal -->
<div id="confirmationModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-[60] p-4 transition-all duration-150">
    <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-2xl shadow-2xl flex flex-col max-h-[90vh] border border-slate-200 dark:border-slate-700">
        <div class="p-5 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-900 rounded-t-2xl">
            <h3 class="text-2xl font-bold text-[#2c2520] dark:text-white"><i class="fa-solid fa-clipboard-check text-emerald-500 mr-2"></i> Konfirmasi Pesanan</h3>
            <button onclick="closeConfirmationModal()" class="text-slate-400 hover:text-[#2c2520] dark:hover:text-white transition"><i class="fa-solid fa-xmark text-2xl"></i></button>
        </div>
        
        <div class="p-5 overflow-y-auto flex-grow text-[#2c2520] dark:text-slate-200">
            <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                <div class="bg-slate-50 dark:bg-slate-900/50 p-3 rounded-xl border border-slate-200 dark:border-slate-700">
                    <span class="text-slate-500 dark:text-slate-400 block mb-1">Tipe Pesanan & Meja</span>
                    <strong class="text-[#2c2520] dark:text-white text-base" id="confirmTableType">-</strong>
                </div>
                <div class="bg-slate-50 dark:bg-slate-900/50 p-3 rounded-xl border border-slate-200 dark:border-slate-700">
                    <span class="text-slate-500 dark:text-slate-400 block mb-1">Pelanggan</span>
                    <strong class="text-[#2c2520] dark:text-white text-base" id="confirmCustomer">-</strong>
                </div>
            </div>

            <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-3 border-b border-slate-200 dark:border-slate-700 pb-2">Rincian Menu (Double Check)</h4>
            <div id="confirmCartItems" class="space-y-3 mb-6">
                <!-- Items injected here -->
            </div>
            
            <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                <div class="flex justify-between items-center text-sm text-slate-500 dark:text-slate-400 mb-2">
                    <span>Metode Pembayaran:</span>
                    <strong class="text-[#2c2520] dark:text-white uppercase" id="confirmPaymentMethod">-</strong>
                </div>
                <div id="confirmDiscountRow" class="flex justify-between items-center text-sm text-emerald-600 dark:text-emerald-400 mb-2 hidden">
                    <span>Diskon Poin:</span>
                    <strong id="confirmDiscount">- Rp 0</strong>
                </div>
                <div class="flex justify-between items-center text-xl font-bold border-t border-slate-200 dark:border-slate-700 pt-3 mt-1">
                    <span class="text-[#2c2520] dark:text-white">Total Tagihan:</span>
                    <strong class="text-brand-600 dark:text-brand-400 text-2xl" id="confirmTotal">Rp 0</strong>
                </div>
            </div>
        </div>
        
        <div class="p-5 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/80 rounded-b-2xl flex gap-3">
            <button onclick="closeConfirmationModal()" class="flex-1 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-white py-3 rounded-xl font-bold transition">Kembali / Edit</button>
            <button onclick="submitOrder()" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white py-3 rounded-xl font-bold shadow-lg transition flex items-center justify-center gap-2 text-lg">
                <i class="fa-solid fa-check-double"></i> Konfirmasi & Bayar
            </button>
        </div>
    </div>
</div>

<!-- Variant Selection Modal -->
<div id="variantModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4 transition-all duration-150">
    <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-lg shadow-2xl flex flex-col max-h-[90vh] border border-slate-200 dark:border-slate-700">
        <div class="p-5 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-900 rounded-t-2xl">
            <h3 class="text-2xl font-bold text-[#2c2520] dark:text-white" id="modalProductName">Product Name</h3>
            <button onclick="closeVariantModal()" class="text-slate-400 hover:text-[#2c2520] dark:hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button>
        </div>
        <div class="p-5 overflow-y-auto flex-grow" id="variantContainer">
            <!-- Variants injected here via JS -->
        </div>
        <div class="p-5 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 rounded-b-2xl flex justify-between items-center">
            <div>
                <span class="text-slate-500 dark:text-slate-400 text-sm">Total Harga Item:</span>
                <div class="text-xl font-bold text-brand-600 dark:text-brand-400" id="modalItemTotal">Rp 0</div>
            </div>
            <button onclick="addSelectedVariantToCart()" class="bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-3 rounded-xl font-bold transition">Tambah ke Pesanan</button>
        </div>
    </div>
</div>

<script>
    let cart = []; // Use array to support multiple same products with different variants
    let currentProduct = null;
    let selectedVariants = {};
    
    // Loyalty Settings from Backend
    const earnRate = {{ $earn_rate }};
    const redeemValue = {{ $redeem_value }};
    const bdayPromoStatus = '{{ $bday_promo_status }}';
    
    function handleCustomerChange() {
        const select = document.getElementById('customer_id');
        const pointSection = document.getElementById('pointSection');
        const availablePointsEl = document.getElementById('availablePoints');
        const usePointsCb = document.getElementById('use_points');
        const bdayAlert = document.getElementById('bdayAlert');
        
        if (select.value) {
            const selectedOpt = select.options[select.selectedIndex];
            const pts = parseInt(selectedOpt.getAttribute('data-points')) || 0;
            const isBirthday = selectedOpt.getAttribute('data-is-birthday') === 'true';
            
            if (isBirthday && bdayPromoStatus === '1') {
                if(bdayAlert) bdayAlert.classList.remove('hidden');
            } else {
                if(bdayAlert) bdayAlert.classList.add('hidden');
            }
            
            availablePointsEl.innerText = pts.toLocaleString('id-ID');
            pointSection.classList.remove('hidden');
            if(pts == 0) {
                usePointsCb.disabled = true;
                usePointsCb.checked = false;
            } else {
                usePointsCb.disabled = false;
            }
        } else {
            pointSection.classList.add('hidden');
            if(bdayAlert) bdayAlert.classList.add('hidden');
            usePointsCb.checked = false;
        }
        updateCartUI();
    }
    function handleProductClick(product) {
        if(product.variants && product.variants.length > 0) {
            openVariantModal(product);
        } else {
            addToCart(product, []);
        }
    }

    let activeCategory = 'all';

    function setCategoryFilter(categoryId) {
        activeCategory = categoryId;
        
        // Update button styles
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.classList.remove('bg-brand-600', 'text-white');
            btn.classList.add('bg-white', 'dark:bg-slate-800', 'text-slate-600', 'dark:text-slate-300');
        });
        
        let activeBtn = document.getElementById('cat-' + categoryId);
        if(activeBtn) {
            activeBtn.classList.remove('bg-white', 'dark:bg-slate-800', 'text-slate-600', 'dark:text-slate-300');
            activeBtn.classList.add('bg-brand-600', 'text-white');
        }
        
        filterProducts();
    }

    function filterProducts() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        const cards = document.querySelectorAll('.product-card');
        
        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            const category = card.getAttribute('data-category');
            
            const matchesQuery = name.includes(query);
            const matchesCategory = (activeCategory === 'all' || category === activeCategory);
            
            if (matchesQuery && matchesCategory) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function openVariantModal(product) {
        currentProduct = product;
        selectedVariants = {}; // reset selections
        document.getElementById('modalProductName').innerText = product.name;
        
        let html = '';
        product.variants.forEach((v, vIndex) => {
            html += `<div class="mb-5">
                <h4 class="font-bold text-[#2c2520] dark:text-slate-200 mb-2">${v.name} ${v.is_required ? '<span class="text-red-500 text-xs">*Wajib</span>' : '<span class="text-slate-400 text-xs">(Opsional)</span>'}</h4>
                <div class="space-y-2">`;
            
            v.options.forEach((opt, oIndex) => {
                let inputType = v.is_required ? 'radio' : 'radio'; // keeping it radio for single choice per variant group, even if optional
                let nameAttr = `variant_${v.id}`;
                let priceText = opt.price_adjustment > 0 ? ` (+Rp${new Intl.NumberFormat('id-ID').format(opt.price_adjustment)})` : '';
                
                html += `
                <label class="flex items-center p-3 border border-slate-200 dark:border-slate-700 rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 transition duration-100">
                    <input type="radio" name="${nameAttr}" value="${opt.id}" class="w-4 h-4 text-brand-600 bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 focus:ring-brand-600" onchange="calculateModalTotal()" ${v.is_required && oIndex === 0 ? 'checked' : ''}>
                    <span class="ml-3 text-sm font-medium text-slate-700 dark:text-slate-300 flex-grow">${opt.name}</span>
                    <span class="text-brand-600 dark:text-brand-400 text-sm font-bold" data-price="${opt.price_adjustment}">${priceText}</span>
                </label>`;
            });
            
            html += `</div></div>`;
        });
        
        document.getElementById('variantContainer').innerHTML = html;
        document.getElementById('variantModal').classList.remove('hidden');
        document.getElementById('variantModal').classList.add('flex');
        calculateModalTotal();
    }

    function closeVariantModal() {
        document.getElementById('variantModal').classList.add('hidden');
        document.getElementById('variantModal').classList.remove('flex');
        currentProduct = null;
    }

    function calculateModalTotal() {
        if(!currentProduct) return;
        let total = parseFloat(currentProduct.price);
        
        currentProduct.variants.forEach(v => {
            let selectedOpt = document.querySelector(`input[name="variant_${v.id}"]:checked`);
            if(selectedOpt) {
                let optId = selectedOpt.value;
                let option = v.options.find(o => o.id == optId);
                if(option) total += parseFloat(option.price_adjustment);
            }
        });
        
        document.getElementById('modalItemTotal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
    }

    function addSelectedVariantToCart() {
        if(!currentProduct) return;
        
        let variants = [];
        let isValid = true;
        
        currentProduct.variants.forEach(v => {
            let selectedOpt = document.querySelector(`input[name="variant_${v.id}"]:checked`);
            if(selectedOpt) {
                let optId = selectedOpt.value;
                let option = v.options.find(o => o.id == optId);
                if(option) {
                    variants.push({
                        variant_name: v.name,
                        option_name: option.name,
                        price_adjustment: option.price_adjustment
                    });
                }
            } else if (v.is_required) {
                isValid = false;
                alert(`Harap pilih opsi untuk ${v.name}`);
            }
        });
        
        if(!isValid) return;
        
        addToCart(currentProduct, variants);
        closeVariantModal();
    }
    
    function addToCart(product, variants) {
        // Calculate item total price including variants
        let itemPrice = parseFloat(product.price);
        let variantString = '';
        variants.forEach(v => {
            itemPrice += parseFloat(v.price_adjustment);
            variantString += `${v.variant_name}: ${v.option_name}|`;
        });
        
        // Find if identical product with identical variants already in cart
        let existingIndex = cart.findIndex(c => c.product.id === product.id && c.variantString === variantString);
        
        if(existingIndex >= 0) {
            cart[existingIndex].quantity++;
        } else {
            cart.push({
                product: product,
                variants: variants,
                variantString: variantString,
                itemPrice: itemPrice,
                quantity: 1
            });
        }
        updateCartUI();
    }
    
    function updateCartQuantity(index, delta) {
        if(cart[index]) {
            cart[index].quantity += delta;
            if(cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }
        }
        updateCartUI();
    }
    
    function updateCartUI() {
        const container = document.getElementById('cartItems');
        const checkoutBtn = document.getElementById('checkoutBtn');
        let subtotal = 0;
        let html = '';
        
        cart.forEach((item, index) => {
            subtotal += item.itemPrice * item.quantity;
            
            // Build hidden inputs for form submission
            let hiddenInputs = `
                <input type="hidden" name="items[${index}][product_id]" value="${item.product.id}">
                <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
            `;
            
            let variantTags = '';
            item.variants.forEach((v, vIndex) => {
                hiddenInputs += `
                    <input type="hidden" name="items[${index}][variants][${vIndex}][variant_name]" value="${v.variant_name}">
                    <input type="hidden" name="items[${index}][variants][${vIndex}][option_name]" value="${v.option_name}">
                    <input type="hidden" name="items[${index}][variants][${vIndex}][price_adjustment]" value="${v.price_adjustment}">
                `;
                variantTags += `<div class="text-[10px] text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-900 inline-block px-1 rounded mr-1 mt-1 border border-slate-200 dark:border-slate-700">${v.option_name}</div>`;
            });

            html += `
                <div class="flex justify-between items-center bg-white dark:bg-slate-800/80 p-3 rounded-xl animate-[fadeIn_0.15s] border border-slate-200 dark:border-slate-700/50 shadow-sm transition">
                    ${hiddenInputs}
                    <div class="flex-1 pr-2">
                        <div class="font-semibold text-sm leading-tight text-[#2c2520] dark:text-slate-200">${item.product.name}</div>
                        ${variantTags ? `<div class="mt-1">${variantTags}</div>` : ''}
                        <div class="text-brand-600 dark:text-brand-400 text-xs font-bold mt-1">Rp ${new Intl.NumberFormat('id-ID').format(item.itemPrice)}</div>
                    </div>
                    <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-900 rounded-lg p-1">
                        <button type="button" onclick="updateCartQuantity(${index}, -1)" class="text-slate-600 dark:text-slate-400 hover:text-[#2c2520] dark:hover:text-white bg-slate-200 dark:bg-slate-700 rounded w-6 h-6 flex items-center justify-center transition-colors">-</button>
                        <span class="font-bold w-4 text-center text-sm text-[#2c2520] dark:text-white">${item.quantity}</span>
                        <button type="button" onclick="updateCartQuantity(${index}, 1)" class="text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 bg-slate-200 dark:bg-slate-700 rounded w-6 h-6 flex items-center justify-center transition-colors">+</button>
                    </div>
                </div>
            `;
        });
        
        if(cart.length > 0) {
            container.innerHTML = html;
            checkoutBtn.disabled = false;
        } else {
            container.innerHTML = '<div class="text-center text-slate-500 mt-10" id="emptyCart">Cart is empty</div>';
            checkoutBtn.disabled = true;
        }
        
        // Handle Loyalty Calculations
        let discount = 0;
        let finalTotal = subtotal;
        const usePointsCb = document.getElementById('use_points');
        const select = document.getElementById('customer_id');
        const discountRow = document.getElementById('discountRow');
        const discountInfo = document.getElementById('discountInfo');
        const cartDiscount = document.getElementById('cartDiscount');
        const earnedRow = document.getElementById('earnedRow');
        const cartEarned = document.getElementById('cartEarned');
        
        if (usePointsCb && usePointsCb.checked && select.value) {
            const selectedOpt = select.options[select.selectedIndex];
            const pts = parseInt(selectedOpt.getAttribute('data-points')) || 0;
            let maxDiscountPossible = pts * redeemValue;
            
            // Cannot discount more than subtotal
            if (maxDiscountPossible > subtotal) {
                discount = subtotal;
            } else {
                discount = maxDiscountPossible;
            }
            finalTotal = subtotal - discount;
            
            discountRow.classList.remove('hidden');
            cartDiscount.innerText = '- Rp ' + new Intl.NumberFormat('id-ID').format(discount);
            
            let usedPts = Math.ceil(discount / redeemValue);
            discountInfo.innerText = `Menukarkan ${usedPts} poin untuk diskon Rp ${new Intl.NumberFormat('id-ID').format(discount)}`;
            discountInfo.classList.remove('hidden');
        } else {
            discountRow.classList.add('hidden');
            if(discountInfo) discountInfo.classList.add('hidden');
        }
        
        if (select && select.value && finalTotal > 0) {
            let earned = Math.floor(finalTotal / earnRate);
            cartEarned.innerText = earned;
            earnedRow.classList.remove('hidden');
        } else {
            if(earnedRow) earnedRow.classList.add('hidden');
        }

        document.getElementById('cartTotal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(finalTotal);
        
        // If confirmation modal is open, update it too
        if(!document.getElementById('confirmationModal').classList.contains('hidden')) {
            renderConfirmationItems();
        }
    }

    // Modal Confirmation Functions
    function showConfirmationModal() {
        if(cart.length === 0) return;
        
        // Populate Table/Type
        const tableSelect = document.querySelector('select[name="table_id"]');
        const tableName = tableSelect.options[tableSelect.selectedIndex]?.text || 'Belum dipilih';
        const orderType = document.querySelector('input[name="order_type"]:checked').value;
        const typeText = orderType === 'dine_in' ? 'Dine In' : 'Takeaway';
        document.getElementById('confirmTableType').innerText = `${typeText} - ${tableName}`;

        // Populate Customer
        const custSelect = document.getElementById('customer_id');
        const custName = custSelect.value ? custSelect.options[custSelect.selectedIndex].text : 'Bukan Member (Guest)';
        document.getElementById('confirmCustomer').innerText = custName;

        // Populate Payment Method
        const paySelect = document.getElementById('payment_method');
        document.getElementById('confirmPaymentMethod').innerText = paySelect.options[paySelect.selectedIndex].text;

        // Render Items and Totals
        renderConfirmationItems();

        // Show Modal
        document.getElementById('confirmationModal').classList.remove('hidden');
        document.getElementById('confirmationModal').classList.add('flex');
    }

    function renderConfirmationItems() {
        const container = document.getElementById('confirmCartItems');
        let html = '';
        let subtotal = 0;

        cart.forEach((item, index) => {
            subtotal += item.itemPrice * item.quantity;
            let variantTags = '';
            item.variants.forEach(v => {
                variantTags += `<span class="text-[10px] text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded mr-1 border border-slate-200 dark:border-slate-700">${v.option_name}</span>`;
            });

            html += `
                <div class="flex justify-between items-center bg-slate-50 dark:bg-slate-850 p-3 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm transition">
                    <div class="flex-1 pr-4">
                        <div class="font-bold text-[#2c2520] dark:text-slate-200 text-sm md:text-base">${item.product.name}</div>
                        ${variantTags ? `<div class="mt-1">${variantTags}</div>` : ''}
                        <div class="text-brand-600 dark:text-brand-400 text-xs font-bold mt-1">@ Rp ${new Intl.NumberFormat('id-ID').format(item.itemPrice)}</div>
                    </div>
                    
                    <div class="flex items-center gap-3 bg-slate-100 dark:bg-slate-900 rounded-lg p-1.5 border border-slate-200 dark:border-slate-700">
                        <button type="button" onclick="updateCartQuantity(${index}, -1)" class="text-slate-600 dark:text-slate-300 hover:text-[#2c2520] dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-700 rounded-md w-8 h-8 flex items-center justify-center transition duration-100 bg-white dark:bg-slate-800 shadow-sm"><i class="fa-solid fa-minus"></i></button>
                        <span class="font-bold w-6 text-center text-base text-[#2c2520] dark:text-white">${item.quantity}</span>
                        <button type="button" onclick="updateCartQuantity(${index}, 1)" class="text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-700 rounded-md w-8 h-8 flex items-center justify-center transition duration-100 bg-white dark:bg-slate-800 shadow-sm"><i class="fa-solid fa-plus"></i></button>
                    </div>
                    
                    <div class="w-24 text-right font-bold text-[#2c2520] dark:text-white ml-4">
                        Rp ${new Intl.NumberFormat('id-ID').format(item.itemPrice * item.quantity)}
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;

        // Apply Discount Logic again for confirmation modal
        let discount = 0;
        let finalTotal = subtotal;
        const usePointsCb = document.getElementById('use_points');
        const select = document.getElementById('customer_id');
        const confirmDiscountRow = document.getElementById('confirmDiscountRow');
        
        if (usePointsCb && usePointsCb.checked && select.value) {
            const pts = parseInt(select.options[select.selectedIndex].getAttribute('data-points')) || 0;
            let maxDiscount = pts * redeemValue;
            discount = maxDiscount > subtotal ? subtotal : maxDiscount;
            finalTotal = subtotal - discount;
            
            confirmDiscountRow.classList.remove('hidden');
            document.getElementById('confirmDiscount').innerText = '- Rp ' + new Intl.NumberFormat('id-ID').format(discount);
        } else {
            confirmDiscountRow.classList.add('hidden');
        }

        document.getElementById('confirmTotal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(finalTotal);
        
        // Auto-close if cart becomes empty from within modal
        if (cart.length === 0) {
            closeConfirmationModal();
        }
    }

    function closeConfirmationModal() {
        document.getElementById('confirmationModal').classList.add('hidden');
        document.getElementById('confirmationModal').classList.remove('flex');
    }

    function submitOrder() {
        // Validation before submit
        const tableSelect = document.querySelector('select[name="table_id"]');
        if(!tableSelect.value) {
            alert('Silakan pilih meja terlebih dahulu!');
            closeConfirmationModal();
            return;
        }
        
        // Prevent double submit
        document.getElementById('orderForm').submit();
    }

    // Switch right sidebar tabs (Standard POS order vs Table Orders)
    function switchSidebarTab(tabName) {
        const tabBtnPos = document.getElementById('tabBtnPos');
        const tabBtnTable = document.getElementById('tabBtnTable');
        const orderForm = document.getElementById('orderForm');
        const tableOrdersContainer = document.getElementById('tableOrdersContainer');
        
        if (tabName === 'pos') {
            tabBtnPos.className = 'flex-1 pb-2 font-bold text-center border-b-2 border-brand-500 text-brand-600 dark:text-brand-400 text-sm flex items-center justify-center gap-1.5 transition focus:outline-none';
            tabBtnTable.className = 'flex-1 pb-2 font-bold text-center border-b-2 border-transparent text-slate-500 hover:text-[#2c2520] dark:hover:text-white text-sm flex items-center justify-center gap-1.5 transition relative focus:outline-none';
            orderForm.classList.remove('hidden');
            tableOrdersContainer.classList.add('hidden');
        } else {
            tabBtnPos.className = 'flex-1 pb-2 font-bold text-center border-b-2 border-transparent text-slate-500 hover:text-[#2c2520] dark:hover:text-white text-sm flex items-center justify-center gap-1.5 transition focus:outline-none';
            tabBtnTable.className = 'flex-1 pb-2 font-bold text-center border-b-2 border-brand-500 text-brand-600 dark:text-brand-400 text-sm flex items-center justify-center gap-1.5 transition relative focus:outline-none';
            orderForm.classList.add('hidden');
            tableOrdersContainer.classList.remove('hidden');
        }
    }

    // Confirm cashier-pending self-service table order payment
    function confirmTableOrderPayment(orderId, paymentMethod, tableName, amount) {
        const methodLabel = paymentMethod === 'cash' ? 'Tunai (Cash)' : 'QRIS (Gateway)';
        if (confirm(`Konfirmasi pembayaran untuk Meja ${tableName} sebesar Rp ${new Intl.NumberFormat('id-ID').format(amount)} via ${methodLabel}?`)) {
            
            fetch(`/pos/table-payment/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    payment_method: paymentMethod
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    window.location.href = data.redirect_url;
                } else {
                    alert('Gagal memproses pembayaran: ' + (data.message || 'Error tidak diketahui'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal menghubungi server: ' + (err.message || 'Koneksi terputus.'));
            });
        }
    }

    // Auto-update stats and table orders every 5 seconds
    function pollLiveStatus() {
        if (document.hidden) return;
        
        fetch('{{ route('pos.live-status') }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) return response.json();
            throw new Error('Failed to fetch live status');
        })
        .then(data => {
            // Update stats
            const statMenuAktif = document.getElementById('statMenuAktif');
            const statPesananBerjalan = document.getElementById('statPesananBerjalan');
            const statPesananSelesai = document.getElementById('statPesananSelesai');
            const statPenjualanShift = document.getElementById('statPenjualanShift');
            
            if (statMenuAktif) statMenuAktif.innerText = data.activeProductsCount;
            if (statPesananBerjalan) statPesananBerjalan.innerText = data.activeOrdersCount;
            if (statPesananSelesai) statPesananSelesai.innerText = data.completedOrdersCount;
            if (statPenjualanShift) {
                statPenjualanShift.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.shiftSales);
            }
            
            // Update table orders badge
            const badgeContainer = document.getElementById('badgeTableOrdersContainer');
            const badgeCount = document.getElementById('badgeTableOrders');
            if (badgeContainer && badgeCount) {
                if (data.unpaidOrdersCount > 0) {
                    badgeCount.innerText = data.unpaidOrdersCount;
                    badgeContainer.classList.remove('hidden');
                } else {
                    badgeContainer.classList.add('hidden');
                }
            }
            
            // Update table orders list html
            const container = document.getElementById('tableOrdersContainer');
            if (container) {
                container.innerHTML = data.unpaidOrdersHtml;
            }
        })
        .catch(err => {
            console.warn('Live poll error:', err);
        });
    }

    // Start polling every 5 seconds
    setInterval(pollLiveStatus, 5000);
</script>
<style>
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
.hide-scroll::-webkit-scrollbar {
    display: none;
}
.hide-scroll {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
@endsection