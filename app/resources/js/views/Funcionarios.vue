<template>
  <div>
    <h2 class="titulo">👥 Funcionários</h2>

    <div class="lista-funcionarios">
      <div v-for="n in 3" :key="n" class="card-funcionario">
        <h3 class="nome">Funcionário {{ n }}</h3>
        <p class="detalhe">Cargo: Técnico</p>
        <p class="detalhe">Email: funcionario{{ n }}@empresa.com</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.titulo {
  font-size: 1.875rem; /* 3xl = 30px approx */
  font-weight: 700;
  margin-bottom: 24px;
  color: #2d3748; /* text-gray-800 */
}

/* Grid responsivo */
.lista-funcionarios {
  display: grid;
  grid-template-columns: 1fr;
  gap: 16px;
}

@media (min-width: 768px) {
  /* md:grid-cols-2 */
  .lista-funcionarios {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1024px) {
  /* lg:grid-cols-3 */
  .lista-funcionarios {
    grid-template-columns: repeat(3, 1fr);
  }
}

.card-funcionario {
  background-color: white;
  box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);
  border-radius: 12px;
  padding: 16px;
  border: 1px solid #f7fafc; /* gray-100 */
  transition: box-shadow 0.3s ease;
}

.card-funcionario:hover {
  box-shadow: 0 4px 12px rgb(0 0 0 / 0.15);
}

.nome {
  font-size: 1.25rem; /* xl = 20px */
  font-weight: 600;
  color: #4a5568; /* text-gray-700 */
  margin-bottom: 8px;
}

.detalhe {
  color: #a0aec0; /* text-gray-500 */
  margin: 2px 0;
}
</style>
