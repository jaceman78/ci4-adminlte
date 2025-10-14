# Sistema de Notificações Toast

## 📢 Implementação Completa

### ✅ Bibliotecas Incluídas:
- **Toastr.js** - CDN incluído em `app/Views/layout/partials/footer.php`
- **toast-notifications.js** - Sistema global customizado

### 🎨 Configuração Global

O sistema está configurado com as seguintes opções padrão:
- Posição: Canto superior direito
- Duração: 5 segundos
- Barra de progresso: Ativada
- Botão fechar: Ativado
- Animações: FadeIn/FadeOut

## 🚀 Como Usar

### 1. Notificações Básicas

#### Usando Funções Simplificadas:
```javascript
// Sucesso
showSuccess('Equipamento criado com sucesso!');
toast.success('Operação concluída!');

// Erro
showError('Não foi possível salvar os dados');
toast.error('Erro ao processar');

// Aviso
showWarning('Atenção: Este equipamento não tem sala');
toast.warning('Verifique os dados');

// Informação
showInfo('Sistema atualizado para versão 2.0');
toast.info('Nova funcionalidade disponível');
```

#### Usando Função Genérica:
```javascript
showToast('success', 'Mensagem de sucesso');
showToast('error', 'Mensagem de erro');
showToast('warning', 'Mensagem de aviso');
showToast('info', 'Mensagem informativa');
```

### 2. Notificações com Título Customizado

```javascript
showSuccess('Dados salvos', 'Parabéns!');
showError('Falha na conexão', 'Erro de Rede');
showWarning('Verifique o formulário', 'Dados Incompletos');
showInfo('Leia a documentação', 'Dica');
```

### 3. Notificações Persistentes (não fecham automaticamente)

```javascript
toast.persistent('error', 'Este erro requer atenção', 'Crítico!');
toast.persistent('info', 'Lembre-se de fazer backup', 'Importante');
```

### 4. Toast de Loading

```javascript
// Mostrar loading
const loading = toast.loading('Salvando dados...');

// Fazer operação assíncrona
$.post('url', data, function(response) {
    // Fechar loading
    toast.hideLoading(loading);
    
    // Mostrar resultado
    toast.success('Dados salvos!');
}).fail(function() {
    toast.hideLoading(loading);
    toast.error('Erro ao salvar');
});
```

### 5. Toast de Progresso

```javascript
// Criar toast de progresso
const progressToast = toast.progress('Upload em andamento...', 0);

// Atualizar progresso
toast.updateProgress(progressToast, 25);
toast.updateProgress(progressToast, 50);
toast.updateProgress(progressToast, 75);

// Finalizar
toast.updateProgress(progressToast, 100);
setTimeout(() => {
    toast.hideLoading(progressToast);
    toast.success('Upload concluído!');
}, 500);
```

### 6. Toast de Confirmação

```javascript
toast.confirm(
    'Tem certeza que deseja eliminar este equipamento?',
    function() {
        // Usuário confirmou
        deleteEquipamento(id);
    },
    function() {
        // Usuário cancelou
        toast.info('Operação cancelada');
    }
);
```

### 7. Toast com Ação

```javascript
toast.show('info', 'Novo equipamento disponível. Clique para ver.');
toastr.options.onclick = function() {
    window.location = '/equipamentos/novo';
};
```

### 8. Limpar Notificações

```javascript
// Limpar todas as notificações
toast.clear();
clearToasts();
```

## 📝 Exemplos de Uso em Equipamentos

### Criar Equipamento
```javascript
$.ajax({
    url: baseUrl + 'equipamentos/createWithSala',
    type: 'POST',
    data: formData,
    success: function(response) {
        $('#equipamentoModal').modal('hide');
        table.ajax.reload();
        
        // Toast de sucesso
        toast.success(response.message || 'Equipamento criado com sucesso!');
    },
    error: function(xhr) {
        const response = JSON.parse(xhr.responseText);
        
        // Toast de erro com mensagens de validação
        if (response.messages) {
            const errors = Object.values(response.messages).join('<br>');
            toast.error(errors, 'Erro de Validação');
        } else {
            toast.error(response.message || 'Erro ao criar equipamento');
        }
    }
});
```

### Atribuir Sala
```javascript
$.post(baseUrl + 'equipamentos/atribuirSala', data, function(response) {
    $('#gerirSalaModal').modal('hide');
    table.ajax.reload();
    
    toast.success(response.message, 'Sala Atribuída!');
    
}).fail(function(xhr) {
    toast.error('Erro ao atribuir sala', 'Falha na Operação');
});
```

### Confirmar Eliminação
```javascript
function deleteEquipamento(id) {
    toast.confirm(
        'Esta ação não pode ser desfeita. Deseja continuar?',
        function() {
            // Mostrar loading
            const loading = toast.loading('Eliminando equipamento...');
            
            $.post(baseUrl + 'equipamentos/delete/' + id, function(response) {
                toast.hideLoading(loading);
                toast.success('Equipamento eliminado!');
                table.ajax.reload();
            }).fail(function() {
                toast.hideLoading(loading);
                toast.error('Erro ao eliminar');
            });
        }
    );
}
```

### Upload com Progresso
```javascript
function uploadFile(file) {
    const formData = new FormData();
    formData.append('file', file);
    
    const progressToast = toast.progress('Enviando arquivo...', 0);
    
    $.ajax({
        url: baseUrl + 'upload',
        type: 'POST',
        data: formData,
        xhr: function() {
            const xhr = new window.XMLHttpRequest();
            
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    toast.updateProgress(progressToast, percentComplete);
                }
            }, false);
            
            return xhr;
        },
        success: function() {
            toast.hideLoading(progressToast);
            toast.success('Arquivo enviado com sucesso!');
        },
        error: function() {
            toast.hideLoading(progressToast);
            toast.error('Erro ao enviar arquivo');
        }
    });
}
```

## 🎨 Customização de Cores

As cores são definidas automaticamente pelo tipo:
- **Success**: Verde (#51a351)
- **Error**: Vermelho (#bd362f)
- **Warning**: Amarelo/Laranja (#f89406)
- **Info**: Azul (#2f96b4)

## 📍 Posições Disponíveis

Para alterar a posição global, edite `toast-notifications.js`:
```javascript
toastr.options.positionClass = "toast-top-right"; // Padrão

// Outras opções:
// "toast-top-left"
// "toast-top-center"
// "toast-top-full-width"
// "toast-bottom-right"
// "toast-bottom-left"
// "toast-bottom-center"
// "toast-bottom-full-width"
```

## ⚙️ Configurações Avançadas

### Alterar Duração
```javascript
toastr.options.timeOut = 10000; // 10 segundos
toastr.options.extendedTimeOut = 2000;
```

### Desabilitar Botão Fechar
```javascript
toastr.options.closeButton = false;
```

### Desabilitar Barra de Progresso
```javascript
toastr.options.progressBar = false;
```

### Permitir HTML nas Mensagens
```javascript
toastr.options.allowHtml = true;
toast.success('<strong>Atenção!</strong> Operação concluída');
```

## 🔧 Troubleshooting

### Toast não aparece
1. Verificar se Toastr.js está carregado
2. Verificar console do navegador para erros
3. Confirmar que toast-notifications.js está incluído após toastr.js

### Múltiplos toasts do mesmo tipo
```javascript
// Ativar prevenção de duplicatas
toastr.options.preventDuplicates = true;
```

### Toast não fecha
```javascript
// Forçar fechamento
toast.clear();
```

## 📦 Arquivos Modificados

1. ✅ `app/Views/layout/partials/footer.php` - Incluído Toastr.js e script global
2. ✅ `public/assets/js/toast-notifications.js` - **NOVO** Sistema global
3. ✅ `public/assets/js/equipamentos.js` - Atualizado função showToast()

## 🎯 Próximos Passos (Opcional)

- [ ] Adicionar toasts em outros controladores (Users, Salas, Escolas, etc.)
- [ ] Implementar toasts para operações em lote
- [ ] Adicionar sons nas notificações
- [ ] Criar tema escuro para toasts
- [ ] Integrar com sistema de notificações do servidor (websockets)

## ✨ Status: IMPLEMENTADO COM SUCESSO! 🎉
