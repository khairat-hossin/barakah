<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Organization Setup - {{ \App\Support\Branding::name() }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --accent: #10b981; --muted: #9ca3af; --line: #e5e7eb; }
        body {
            background-color: #f5f6f8;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 40px 16px;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            color: #1f2937;
        }
        .setup-wrap { width: 100%; max-width: 720px; }

        /* Brand */
        .setup-brand { text-align: center; margin-bottom: 22px; }
        .setup-brand img { height: 38px; }

        /* Stepper */
        .stepper { display: flex; margin-bottom: 24px; }
        .s-item { flex: 1; position: relative; text-align: center; }
        .s-item:not(:first-child)::before {
            content: ''; position: absolute; top: 17px; left: -50%; width: 100%; height: 2px;
            background: var(--line); z-index: 0;
        }
        .s-item.done:not(:first-child)::before,
        .s-item.active:not(:first-child)::before { background: var(--accent); }
        .s-circle {
            position: relative; z-index: 1; width: 36px; height: 36px; border-radius: 50%;
            margin: 0 auto 6px; display: flex; align-items: center; justify-content: center;
            background: #fff; border: 2px solid var(--line); color: var(--muted);
            font-weight: 600; font-size: 14px; transition: all .2s;
        }
        .s-item.active .s-circle { border-color: var(--accent); color: var(--accent); }
        .s-item.done .s-circle { background: var(--accent); border-color: var(--accent); color: #fff; }
        .s-label { font-size: 13px; color: var(--muted); }
        .s-item.active .s-label { color: #1f2937; font-weight: 600; }
        .s-item.done .s-label { color: var(--accent); }

        /* Card */
        .setup-card {
            background: #fff; border: 1px solid #eceef1; border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04); padding: 28px 30px;
        }
        .step-title { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
        .step-subtitle { font-size: 14px; color: #6b7280; margin-bottom: 22px; }

        .step-pane { display: none; }
        .step-pane.active { display: block; animation: fade .25s ease; }
        @keyframes fade { from { opacity: 0; } to { opacity: 1; } }

        .form-label { font-weight: 600; font-size: 13px; margin-bottom: 6px; }
        .form-control, .form-select { font-size: 14px; padding: 9px 12px; border-radius: 8px; }

        /* Footer */
        .step-footer {
            display: flex; align-items: center; justify-content: space-between;
            border-top: 1px solid var(--line); margin-top: 22px; padding-top: 18px;
        }
        .step-counter { font-size: 13px; color: #6b7280; }
        .btn-nav {
            display: inline-flex; align-items: center; gap: 6px; font-size: 14px; font-weight: 600;
            padding: 8px 16px; border-radius: 9px; border: 1px solid #d1d5db; background: #fff; color: #1f2937;
        }
        .btn-nav:hover { background: #f9fafb; }
        .btn-nav:disabled { color: #c4c8ce; border-color: #ececef; cursor: not-allowed; }
        .btn-nav.primary { border-color: #1f2937; }
        .btn-nav.submit { border-color: var(--accent); color: var(--accent); }
        .btn-nav.submit:hover { background: #ecfdf5; }

        @media (max-width: 576px) {
            .setup-card { padding: 20px; }
            .s-label { font-size: 11px; }
        }
    </style>
</head>
<body>
    <div class="setup-wrap">
        <div class="setup-brand">
            <img src="{{ \App\Support\Branding::url('logo-name.png') }}" alt="{{ \App\Support\Branding::name() }}">
        </div>

        <!-- Stepper -->
        <div class="stepper" id="stepper">
            <div class="s-item active" data-step="1"><div class="s-circle">1</div><div class="s-label">Basic</div></div>
            <div class="s-item" data-step="2"><div class="s-circle">2</div><div class="s-label">Contact</div></div>
            <div class="s-item" data-step="3"><div class="s-circle">3</div><div class="s-label">Address</div></div>
            <div class="s-item" data-step="4"><div class="s-circle">4</div><div class="s-label">Vision</div></div>
        </div>

        <div class="setup-card">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('setup.store') }}" id="setupForm">
                @csrf

                <!-- Step 1: Basic Information -->
                <div class="step-pane active" data-step="1">
                    <div class="step-title">Basic Information</div>
                    <div class="step-subtitle">Tell us about your organization.</div>

                    <div class="mb-3">
                        <label class="form-label">Organization Name (English) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="organization_name_en" value="{{ old('organization_name_en', $org?->organization_name_en) }}" required>
                        @error('organization_name_en')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Organization Name (Bangla)</label>
                        <input type="text" class="form-control" name="organization_name_bn" value="{{ old('organization_name_bn', $org?->organization_name_bn) }}">
                        @error('organization_name_bn')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Short Name</label>
                                <input type="text" class="form-control" name="short_name" value="{{ old('short_name', $org?->short_name) }}">
                                @error('short_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Organization Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="organization_type" required>
                                    <option value="">Select Type</option>
                                    <option value="coop" {{ old('organization_type', $org?->organization_type) == 'coop' ? 'selected' : '' }}>Cooperative</option>
                                    <option value="ngo" {{ old('organization_type', $org?->organization_type) == 'ngo' ? 'selected' : '' }}>NGO</option>
                                    <option value="mutual" {{ old('organization_type', $org?->organization_type) == 'mutual' ? 'selected' : '' }}>Mutual Organization</option>
                                    <option value="association" {{ old('organization_type', $org?->organization_type) == 'association' ? 'selected' : '' }}>Association</option>
                                    <option value="other" {{ old('organization_type', $org?->organization_type) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('organization_type')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="step-footer">
                        <button type="button" class="btn-nav" disabled>← Back</button>
                        <span class="step-counter">Step 1 of 4</span>
                        <button type="button" class="btn-nav primary" onclick="goToStep(2)">Next →</button>
                    </div>
                </div>

                <!-- Step 2: Contact Information -->
                <div class="step-pane" data-step="2">
                    <div class="step-title">Contact Information</div>
                    <div class="step-subtitle">How can we reach your organization?</div>

                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" value="{{ old('email', $org?->email) }}" required>
                        @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="mobile_number" value="{{ old('mobile_number', $org?->mobile_number) }}" required>
                        @error('mobile_number')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Secondary Mobile</label>
                        <input type="text" class="form-control" name="secondary_mobile" value="{{ old('secondary_mobile', $org?->secondary_mobile) }}">
                        @error('secondary_mobile')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="step-footer">
                        <button type="button" class="btn-nav" onclick="goToStep(1)">← Back</button>
                        <span class="step-counter">Step 2 of 4</span>
                        <button type="button" class="btn-nav primary" onclick="goToStep(3)">Next →</button>
                    </div>
                </div>

                <!-- Step 3: Address -->
                <div class="step-pane" data-step="3">
                    <div class="step-title">Address</div>
                    <div class="step-subtitle">Where is your organization located?</div>

                    <div class="mb-3">
                        <label class="form-label">Address Line</label>
                        <input type="text" class="form-control" name="address_line" value="{{ old('address_line', $org?->address_line) }}">
                        @error('address_line')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Village/Area</label>
                                <input type="text" class="form-control" name="village_area" value="{{ old('village_area', $org?->village_area) }}">
                                @error('village_area')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Post Office</label>
                                <input type="text" class="form-control" name="post_office" value="{{ old('post_office', $org?->post_office) }}">
                                @error('post_office')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Union/Ward</label>
                                <input type="text" class="form-control" name="union_ward" value="{{ old('union_ward', $org?->union_ward) }}">
                                @error('union_ward')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Upazila</label>
                                <input type="text" class="form-control" name="upazila" value="{{ old('upazila', $org?->upazila) }}">
                                @error('upazila')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">District</label>
                                <input type="text" class="form-control" name="district" value="{{ old('district', $org?->district) }}">
                                @error('district')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Division</label>
                                <input type="text" class="form-control" name="division" value="{{ old('division', $org?->division) }}">
                                @error('division')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Postal Code</label>
                        <input type="text" class="form-control" name="postal_code" value="{{ old('postal_code', $org?->postal_code) }}">
                        @error('postal_code')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="step-footer">
                        <button type="button" class="btn-nav" onclick="goToStep(2)">← Back</button>
                        <span class="step-counter">Step 3 of 4</span>
                        <button type="button" class="btn-nav primary" onclick="goToStep(4)">Next →</button>
                    </div>
                </div>

                <!-- Step 4: Vision & Mission -->
                <div class="step-pane" data-step="4">
                    <div class="step-title">Vision &amp; Mission</div>
                    <div class="step-subtitle">What is your organization's purpose?</div>

                    <div class="mb-3">
                        <label class="form-label">Motto</label>
                        <input type="text" class="form-control" name="motto" placeholder="e.g., Together We Prosper" value="{{ old('motto', $org?->motto) }}">
                        @error('motto')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Vision Statement</label>
                        <textarea class="form-control" name="vision_statement" rows="3" placeholder="Describe your organization's vision">{{ old('vision_statement', $org?->vision_statement) }}</textarea>
                        @error('vision_statement')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mission Statement</label>
                        <textarea class="form-control" name="mission_statement" rows="3" placeholder="Describe your organization's mission">{{ old('mission_statement', $org?->mission_statement) }}</textarea>
                        @error('mission_statement')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <p class="text-body-secondary small mb-0">Share structure and financial details can be configured later from <strong>Organization Profile</strong>.</p>

                    <div class="step-footer">
                        <button type="button" class="btn-nav" onclick="goToStep(3)">← Back</button>
                        <span class="step-counter">Step 4 of 4</span>
                        <button type="submit" class="btn-nav submit">Complete Setup ➤</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function goToStep(step) {
            document.querySelectorAll('.step-pane').forEach(p => p.classList.toggle('active', +p.dataset.step === step));

            document.querySelectorAll('#stepper .s-item').forEach(item => {
                const n = +item.dataset.step;
                item.classList.remove('active', 'done');
                if (n < step) {
                    item.classList.add('done');
                    item.querySelector('.s-circle').innerHTML = '&#10003;'; // check
                } else {
                    item.querySelector('.s-circle').textContent = n;
                    if (n === step) item.classList.add('active');
                }
            });

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        goToStep(1);
    </script>
</body>
</html>
