<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subscription Packages - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-gray-900">{{ config('app.name') }}</h1>
                    </div>
                    <nav class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-600 hover:text-gray-900">Logout</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Login</a>
                            <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Register</a>
                        @endauth
                    </nav>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Choose Your Plan</h2>
                    <p class="text-lg text-gray-600">Select the perfect plan for your needs</p>
                </div>

                <!-- Plans Grid -->
                <div id="plans-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Plans will be loaded here via JavaScript -->
                </div>

                <!-- Loading State -->
                <div id="loading-state" class="text-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                    <p class="mt-4 text-gray-600">Loading plans...</p>
                </div>

                <!-- Error State -->
                <div id="error-state" class="hidden text-center py-12">
                    <p class="text-red-600">Failed to load plans. Please try again.</p>
                    <button onclick="loadPlans()" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Retry
                    </button>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <p class="text-center text-gray-600">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </footer>
    </div>

    <!-- Plan Modal -->
    <div id="plan-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="modal-title">Subscribe to Plan</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="modal-content">
                    <!-- Modal content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        let plans = [];
        let currentUser = null;

        async function loadPlans() {
            const loadingState = document.getElementById('loading-state');
            const errorState = document.getElementById('error-state');
            const plansContainer = document.getElementById('plans-container');

            loadingState.classList.remove('hidden');
            errorState.classList.add('hidden');
            plansContainer.innerHTML = '';

            try {
                const response = await fetch('/api/v1/plans', {
                    headers: {
                        'Accept': 'application/json',
                        @auth
                        'Authorization': 'Bearer {{ auth()->user()->currentAccessToken()->plainTextToken ?? "" }}',
                        @endauth
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to fetch plans');
                }

                const data = await response.json();
                plans = data.data || [];

                displayPlans(plans);
                loadingState.classList.add('hidden');

            } catch (error) {
                console.error('Error loading plans:', error);
                loadingState.classList.add('hidden');
                errorState.classList.remove('hidden');
            }
        }

        function displayPlans(plans) {
            const plansContainer = document.getElementById('plans-container');
            plansContainer.innerHTML = '';

            if (plans.length === 0) {
                plansContainer.innerHTML = '<p class="text-center text-gray-600 col-span-full">No plans available.</p>';
                return;
            }

            plans.forEach(plan => {
                const planCard = createPlanCard(plan);
                plansContainer.appendChild(planCard);
            });
        }

        function createPlanCard(plan) {
            const card = document.createElement('div');
            card.className = 'bg-white rounded-lg shadow-md p-6 border border-gray-200';

            const isPopular = plan.is_featured;
            const badgeClass = isPopular ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-800';

            card.innerHTML = `
                <div class="text-center">
                    ${isPopular ? `<span class="inline-block px-3 py-1 text-xs font-semibold rounded-full ${badgeClass} mb-4">Most Popular</span>` : ''}
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">${plan.name}</h3>
                    <p class="text-gray-600 mb-4">${plan.description || 'No description available'}</p>
                    <div class="text-3xl font-bold text-gray-900 mb-4">
                        $${plan.price || '0.00'}
                        <span class="text-lg font-normal text-gray-600">/${plan.billing_period}</span>
                    </div>
                    <ul class="text-sm text-gray-600 mb-6 space-y-2">
                        <li>• Billing: ${plan.billing_period}</li>
                        <li>• Interval: ${plan.billing_interval}</li>
                        <li>• Type: ${plan.type}</li>
                    </ul>
                    <button onclick="subscribeToPlan(${plan.id})"
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200">
                        Subscribe Now
                    </button>
                </div>
            `;

            return card;
        }

        async function subscribeToPlan(planId) {
            @guest
                // Redirect to login if not authenticated
                window.location.href = '{{ route("login") }}';
                return;
            @endguest

            const plan = plans.find(p => p.id == planId);
            if (!plan) return;

            openModal(plan);
        }

        function openModal(plan) {
            const modal = document.getElementById('plan-modal');
            const modalTitle = document.getElementById('modal-title');
            const modalContent = document.getElementById('modal-content');

            modalTitle.textContent = `Subscribe to ${plan.name}`;
            modalContent.innerHTML = `
                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-md">
                        <h4 class="font-semibold text-gray-900 mb-2">Plan Details</h4>
                        <p class="text-gray-600">${plan.description || 'No description available'}</p>
                        <p class="text-lg font-semibold text-gray-900 mt-2">$${plan.price || '0.00'} / ${plan.billing_period}</p>
                    </div>

                    <form id="subscription-form" onsubmit="submitSubscription(event, ${plan.id})">
                        <div class="space-y-4">
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                                <input type="number" id="quantity" name="quantity" min="1" value="1"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div class="flex space-x-4">
                                <button type="button" onclick="closeModal()"
                                        class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400 transition duration-200">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200">
                                    Subscribe
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            `;

            modal.classList.remove('hidden');
        }

        function closeModal() {
            const modal = document.getElementById('plan-modal');
            modal.classList.add('hidden');
        }

        async function submitSubscription(event, planId) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);
            const quantity = formData.get('quantity');

            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.textContent = 'Processing...';
            submitButton.disabled = true;

            try {
                const response = await fetch('/api/v1/subscriptions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': 'Bearer {{ auth()->user()->currentAccessToken()->plainTextToken ?? "" }}',
                    },
                    body: JSON.stringify({
                        plan_id: planId,
                        quantity: parseInt(quantity),
                        status: 'active'
                    })
                });

                if (!response.ok) {
                    throw new Error('Failed to create subscription');
                }

                const data = await response.json();

                // Show success message
                alert('Subscription created successfully!');

                // Close modal and redirect to dashboard
                closeModal();
                window.location.href = '{{ route("dashboard") }}';

            } catch (error) {
                console.error('Error creating subscription:', error);
                alert('Failed to create subscription. Please try again.');
            } finally {
                submitButton.textContent = originalText;
                submitButton.disabled = false;
            }
        }

        // Load plans when page loads
        document.addEventListener('DOMContentLoaded', loadPlans);

        // Close modal when clicking outside
        document.getElementById('plan-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>
