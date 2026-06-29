# Sobre a tecnologia
O site precisará ser constuído usando o framework Web Awesome
https://webawesome.com/docs/components

O site deve ser multiidioma, e é preciso que seja fácil de traduzir para outros idiomas.

O site deve ser construído em symfony 8, na sua ultima versão.


Você só deve usar os componentes e as possibilidades disponíveis em Web Awesome. Caso não tenha um componente para alguma funcionalidade, me avise.

Não use CSS inline no código twig/html, nem outras práticas ruins como essa.

Contrua estaticamente a home e a página de detalhe de um protudo fictício.

A página fictícia de produtos deve ser contruída copiando os dados dessa página:
https://www.qiaosenpresses.com/c-frame-single-crank-deep-throat-presses-2-product/

Inspire-se nessa forma de contruíd, com os layout existentes a partir daqui:
https://webawesome.com/docs/patterns

use os efeitos de surgimento e de animação sutis, mas use-os.

Preciso que este layour seja link, primoroso, extremamente elegante, moderno.
O cliente que o encomendou, quando ver, precisa pensar que deu muito trabalho para ser feito.

# Sobre o visual

Quanto a disposição dos elementos na tela, inspire-se neste:
https://wp.ditsolution.net/techno/
https://wp.ditsolution.net/techno/it-solution-05/
https://wp.ditsolution.net/techno/it-solution-04/
Nestes layouts, lembre-se de usar as cores do cliente.


Atue como um desenvolvedor especialista em Symfony, Twig e WebAwesome. Siga as regras abaixo rigorosamente para escrever e revisar os códigos do projeto.

## Regras de Backend (Symfony e Doctrine)
* Mantenha os Controllers simples. Eles devem apenas receber as requisições e devolver as respostas.
* Coloque todas as regras de negócio em classes de serviço.
* Use a injeção de dependência do Symfony. Não instancie classes usando a palavra 'new' dentro dos controllers.
* Escreva consultas no Doctrine usando JOINs para buscar dados relacionados de uma só vez. Evite sempre o problema N+1.
* Leia todas as configurações e credenciais do arquivo .env. Não escreva chaves ou senhas no código PHP.
* Crie e valide formulários usando o componente Form do Symfony. Não construa formulários de forma manual no PHP.

## Regras de Frontend (Twig)
* Use os arquivos .html.twig apenas para exibir dados. Não coloque lógicas complexas ou consultas ao banco de dados nestes arquivos.
* Crie um layout base e utilize as tags 'extends' e 'block' do Twig. Não duplique blocos de código em várias páginas.
* Gere todas as URLs usando a função 'path()'. Não escreva links com URLs fixas.
* Utilize os filtros e as funções nativas do Twig para formatar textos e datas diretamente no template.

## Regras de Interface e Assets (CSS e WebAwesome)
* Crie o estilo visual em arquivos CSS separados. Não use CSS inline nas tags HTML.
* Otimize os arquivos CSS e JavaScript usando AssetMapper ou Webpack Encore. Não coloque arquivos soltos na pasta pública.
* Priorize a semântica do HTML e a acessibilidade, usando as tags corretas para cada componente do WebAwesome.
* Adicione classes específicas para criar estilos novos. Não sobrescreva as classes globais do WebAwesome.



# Cores

preciso desse layout em cor com fundo branco e com os detalhe cinza e vermelho  
#D51C23
#5D5C5E

Pode ter o tema escuro, mas o claro precisa ser dessas cores


# Mapa
sobre o mapa, pode user esse:
<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3704.126178620653!2d-48.15148342472135!3d-21.814055180039055!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94b8f3ca207bffe5%3A0x8f549c30f9c2e200!2sPressmatik%20Prensas%20e%20Equipamentos%20Hidr%C3%A1ulicos!5e0!3m2!1spt-BR!2sbr!4v1782507981053!5m2!1spt-BR!2sbr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>

# conteúdo
Links e contúdos reais podem ser pegos daqui:
https://linktr.ee/pressmatikoficial


# os layouts da home
As sessoes devem se aproximar ao maximo desses layouts:
documentos/layout
sendo que o video da sessào de serviços, é este:
https://media.istockphoto.com/id/2257082678/pt/v%C3%ADdeo/close-up-of-bending-machine-in-the-factory.mp4?s=mp4-640x640-is&k=20&c=WxZ-E7uNG1JIeLX_FPappwForPp8MBNSAwWrLHpYX3c=

