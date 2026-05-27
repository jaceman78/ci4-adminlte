# Correção: Local de Comparecimento no Email de Convocatória

**Data**: 31/03/2026  
**Problema**: O email de convocatória não mencionava o local onde os docentes deveriam comparecer, apenas a antecedência de tempo.

## Situação Encontrada

### ✅ PDF (Correto)
O PDF da convocatória já tinha a lógica correta:
- **6º ano**: "Todos os docentes convocados devem comparecer na **Coordenação da escola** 45 minutos antes..."
- **Outros anos**: "Todos os docentes convocados devem comparecer no **Secretariado de Exames** 45 minutos antes..."

### ❌ Email (Incompleto)
O email apenas dizia:
- "Deverá comparecer com **45 minutos de antecedência**..."
- ⚠️ **Faltava informar o LOCAL** onde comparecer

## Regra de Negócio

Para provas do **6º ano**:
- ✅ Vigilantes → **Coordenação da escola**
- ✅ Apoio TIC → **Coordenação da escola**
- ✅ Verificação Calculadoras → **Coordenação da escola**

Para **outros anos** (4º, 9º, 11º, 12º):
- ✅ Vigilantes → **Secretariado de Exames**
- ✅ Apoio TIC → **Secretariado de Exames**
- ✅ Verificação Calculadoras → **Secretariado de Exames**

⏰ **Antecedência**: Sempre **45 minutos** para todas as provas

## Correção Aplicada

### Arquivo: `app/Views/emails/convocatoria_exame.php`

**Antes**:
```php
<?php
// Lógica de antecedência:
// - Prova Ensaio 6º ano: 45 minutos
// - Prova Ensaio outros anos: 30 minutos
// - Outras provas: 45 minutos
if ($convocatoria['fase'] === 'Prova Ensaio') {
    $minutosAntecedencia = ($convocatoria['ano_escolaridade'] == 6) ? 45 : 30;
    $textoEvento = 'prova';
} else {
    $minutosAntecedencia = 45;
    $textoEvento = 'exame';
}
?>
<strong>⏰ Importante:</strong> Deverá comparecer com <strong><?= $minutosAntecedencia ?> minutos de antecedência</strong> ao horário de início da <?= $textoEvento ?> para receber as instruções necessárias.
```

**Depois**:
```php
<?php
// Todos os docentes devem comparecer 45 minutos antes do início
$minutosAntecedencia = 45;
$textoEvento = ($convocatoria['fase'] === 'Prova Ensaio') ? 'prova' : 'exame';

// Local de comparecimento:
// - Coordenação da escola: Para 6º ano (vigilantes, equipas de apoio, verificação calculadoras)
// - Secretariado de Exames: Para outros anos
if ($convocatoria['ano_escolaridade'] == 6) {
    $localComparecimento = 'Coordenação da escola';
} else {
    $localComparecimento = 'Secretariado de Exames';
}
?>
<strong>⚠ IMPORTANTE:</strong> Todos os docentes convocados devem comparecer na <strong><?= $localComparecimento ?></strong> com <strong><?= $minutosAntecedencia ?> minutos de antecedência</strong> ao horário de início da <?= $textoEvento ?>, para receberem as instruções necessárias.
```

## Mudanças Implementadas

1. ✅ **Simplificado** lógica de antecedência: sempre 45 minutos
2. ✅ **Adicionado** variável `$localComparecimento` baseada no ano de escolaridade
3. ✅ **Melhorado** texto para incluir o local de comparecimento
4. ✅ **Uniformizado** formatação: mesma estrutura do PDF

## Consistência entre PDF e Email

Agora ambos os documentos seguem a mesma lógica e apresentam a mesma informação:

| Documento | 6º ano | Outros Anos |
|-----------|--------|-------------|
| **PDF** | Coordenação da escola | Secretariado de Exames |
| **Email** | Coordenação da escola | Secretariado de Exames |

## Arquivos Modificados

1. ✅ `app/Views/emails/convocatoria_exame.php` - Adicionado local de comparecimento

## Resultado Final

Os professores agora recebem emails com informação completa:

### Exemplo para prova do 6º ano:
> **⚠ IMPORTANTE:** Todos os docentes convocados devem comparecer na **Coordenação da escola** com **45 minutos de antecedência** ao horário de início do exame, para receberem as instruções necessárias.

### Exemplo para prova do 9º ano:
> **⚠ IMPORTANTE:** Todos os docentes convocados devem comparecer no **Secretariado de Exames** com **45 minutos de antecedência** ao horário de início do exame, para receberem as instruções necessárias.
