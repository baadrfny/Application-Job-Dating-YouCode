{% extends "front/layout.twig" %}

{% block title %}Job Dating - Find Your Opportunity{% endblock %}

{% block content %}
<div class="container-fluid px-4 py-5">
    <!-- Header -->
    <div class="row mb-5">
        <div class="col-12">
            <h1 class="display-5 fw-bold mb-3">Trouvez votre opportunité professionnelle</h1>
            <div class="d-flex justify-content-between align-items-center">
                <p class="lead text-muted mb-0">{{ offers|length }} offre(s) disponible(s)</p>
                
                <!-- Search Bar -->
                <div class="w-50">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 ps-0" 
                               id="searchInput" placeholder="Rechercher par titre, entreprise ou description...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-filter me-2"></i>Filtres
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Entreprise</label>
                            <select class="form-select" id="companyFilter">
                                <option value="">Toutes les entreprises</option>
                                {% for company in companies %}
                                <option value="{{ company.id }}">{{ company.nom }}</option>
                                {% endfor %}
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Type de contrat</label>
                            <select class="form-select" id="contractFilter">
                                <option value="">Tous les types</option>
                                <option value="CDI">CDI</option>
                                <option value="CDD">CDD</option>
                                <option value="Freelance">Freelance</option>
                                <option value="Stage">Stage</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Localisation</label>
                            <select class="form-select" id="locationFilter">
                                <option value="">Toutes les villes</option>
                                <option value="Casablanca">Casablanca</option>
                                <option value="Rabat">Rabat</option>
                                <option value="Marrakech">Marrakech</option>
                                <option value="Tanger">Tanger</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Job Offers Grid -->
    <div class="row" id="offersContainer">
        {% for offer in offers %}
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                <div class="card-body p-4">
                    <!-- Job Title & Company -->
                    <div class="mb-3">
                        <h5 class="card-title fw-bold mb-1">{{ offer.titre }}</h5>
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded-circle p-2 me-2">
                                <i class="fas fa-building text-primary"></i>
                            </div>
                            <span class="text-muted">{{ offer.entreprise_nom }}</span>
                        </div>
                    </div>

                    <!-- Contract & Location -->
                    <div class="d-flex gap-2 mb-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                            {{ offer.type_contrat }}
                        </span>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">
                            <i class="fas fa-map-marker-alt me-1"></i>{{ offer.localisation }}
                        </span>
                    </div>

                    <!-- Description -->
                    <p class="card-text text-muted mb-4">
                        {{ offer.description|slice(0, 120) }}...
                    </p>

                    <!-- Skills -->
                    {% if offer.competences %}
                    <div class="mb-4">
                        <p class="small fw-semibold mb-2">Compétences requises:</p>
                        <div class="d-flex flex-wrap gap-2">
                            {% set skills = offer.competences|split(',') %}
                            {% for skill in skills|slice(0, 3) %}
                            <span class="badge bg-light text-dark border">{{ skill|trim }}</span>
                            {% endfor %}
                            {% if skills|length > 3 %}
                            <span class="badge bg-light text-dark border">+{{ skills|length - 3 }}</span>
                            {% endif %}
                        </div>
                    </div>
                    {% endif %}

                    <!-- Footer -->
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <small class="text-muted">
                            <i class="far fa-clock me-1"></i>
                            {{ offer.created_at|date('d/m/Y') }}
                        </small>
                        <a href="/annonces/{{ offer.id }}" class="btn btn-primary btn-sm px-4">
                            Voir détails <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        {% else %}
        <!-- Empty State -->
        <div class="col-12">
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-briefcase fa-4x text-muted opacity-25"></i>
                </div>
                <h3 class="text-muted">Aucune offre disponible</h3>
                <p class="text-muted">Veuillez vérifier ultérieurement.</p>
            </div>
        </div>
        {% endfor %}
    </div>

    <!-- Stats -->
    <div class="row mt-5 pt-5 border-top">
        <div class="col-12">
            <div class="text-center">
                <div class="row justify-content-center">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3">
                            <h3 class="fw-bold text-primary">{{ offers|length }}</h3>
                            <p class="text-muted mb-0">Offres actives</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3">
                            <h3 class="fw-bold text-primary">{{ companies|length }}</h3>
                            <p class="text-muted mb-0">Entreprises</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3">
                            <h3 class="fw-bold text-primary">
                                {% set contract_types = {} %}
                                {% for offer in offers %}
                                    {% set contract_types = contract_types|merge({(offer.type_contrat): true}) %}
                                {% endfor %}
                                {{ contract_types|keys|length }}
                            </h3>
                            <p class="text-muted mb-0">Types de contrat</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}
.transition-all {
    transition: all 0.3s ease;
}
.badge {
    padding: 6px 12px;
    font-weight: 500;
}
</style>
{% endblock %}