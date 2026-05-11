@extends('layouts.app')

@section('title', 'Contact')

@section('content')

<div class="page-header">
    <h2 class="mb-0"><i class="bi bi-envelope me-2"></i>Contact</h2>
</div>

<div class="row g-4 justify-content-center">

    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Developer</h5>
                <table class="table table-borderless table-sm">
                    <tr>
                        <th class="text-muted pe-3">Name</th>
                        <td>Chris Vega</td>
                    </tr>
                    <tr>
                        <th class="text-muted pe-3">Email</th>
                        <td>
                            <a href="mailto:christofer.nvr@gmail.com">
                                christofer.nvr@gmail.com
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted pe-3">University</th>
                        <td>Universidad de Granada</td>
                    </tr>
                    <tr>
                        <th class="text-muted pe-3">Course</th>
                        <td>Tecnologías Web</td>
                    </tr>
                    <tr>
                        <th class="text-muted pe-3">Year</th>
                        <td>2025–2026</td>
                    </tr>
                </table>

                <hr>

                <a class="btn btn-be" href="{{ asset('como_se_hizo.pdf') }}" target="_blank">
                    <i class="bi bi-file-pdf me-1"></i>Download Project Report
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">About BookXchange</h5>
                <p class="text-muted">
                    BookXchange is a circular-economy platform for exchanging second-hand books
                    between readers. Users can list books they no longer need and request exchanges
                    with other members of the community.
                </p>
                <p class="text-muted mb-0">
                    This project was developed as a practical assignment for the Web Technologies
                    course at the University of Granada.
                </p>
            </div>
        </div>
    </div>

</div>

@endsection
