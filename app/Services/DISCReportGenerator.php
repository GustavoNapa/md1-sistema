<?php

namespace App\Services;

class DISCReportGenerator
{
    private array $profilesData = [
        'D' => [
            'nome' => 'Dominância',
            'titulo' => 'DOMINÂNCIA (D) - EXECUTOR',
            'descricao' => 'Você possui um perfil com forte tendência à Dominância. Naturalmente orientado a resultados, você dificilmente se sente confortável quando não há metas claras ou desafios pela frente. Gosta de ter controle das situações, decidir com agilidade e ver progresso concreto nas suas ações.',
            'subcaracteristicas' => [
                'Determinação' => 'Visão objetiva, foco nos resultados, não desiste. Forte orientação a metas e resistência diante de obstáculos.',
                'Independência' => 'Prefere agir por conta própria, frustrado com regras excessivas. Preferência por autonomia decisória e liberdade de ação.',
                'Automotivação' => 'Sempre ativo, impaciente, persegue ambições. Energia constante, proatividade e busca contínua por novos desafios.',
            ],
            'habilidades_basicas' => 'O Executor possui habilidade natural para definir metas claras e mobilizar recursos para alcançá-las. Demonstra agilidade mental, raciocínio prático e capacidade de agir mesmo sob incerteza. Sua disposição para assumir riscos calculados o torna eficiente em cenários competitivos ou de mudança acelerada. Além disso, apresenta habilidade em priorizar tarefas estratégicas, eliminando distrações e mantendo foco no que gera resultado concreto.',
            'habilidades_comuns' => 'No dia a dia, você tende a ser objetivo, direto e prático. Não costuma enrolar para tomar decisões e prefere agir rapidamente a ficar analisando excessivamente. Quando identifica uma oportunidade ou um problema, sua postura é de ação imediata. Você se sente energizado quando está liderando projetos, resolvendo impasses ou buscando crescimento. Esse perfil faz com que você demonstre autoconfiança e firmeza nas suas posições. Ao mesmo tempo, pode ficar impaciente quando percebe lentidão, indecisão ou falta de comprometimento ao seu redor. Para você, eficiência não é apenas importante — é essencial.',
            'vantagens' => 'O perfil Dominância apresenta fortes vantagens no contexto profissional, especialmente em posições estratégicas, empreendedoras ou de liderança. Sua capacidade de agir rapidamente diante de desafios proporciona vantagem competitiva em ambientes dinâmicos. Ele demonstra coragem para tomar decisões difíceis, inclusive quando envolvem riscos ou impopularidade momentânea. Interpessoalmente, transmite segurança e firmeza, o que gera percepção de autoridade e credibilidade. É visto como alguém que resolve problemas, não como alguém que os prolonga. Em liderança, inspira pelo exemplo de produtividade, foco e determinação. Sua postura assertiva ajuda equipes a manterem direcionamento claro, metas objetivas e senso de urgência.',
            'desvantagens' => 'A intensidade do perfil Dominância pode gerar desafios significativos quando não há equilíbrio. Sua impaciência com lentidão, erros ou indecisão pode ser interpretada como insensibilidade ou autoritarismo. A tendência a priorizar resultados acima de processos e pessoas pode afetar o clima organizacional se não houver consciência emocional. Pode assumir controle excessivo, centralizando decisões e limitando a participação da equipe. Em situações de conflito, pode adotar postura confrontadora, o que pode gerar resistência ou desgaste relacional. Sua comunicação direta pode soar dura ou abrupta. Outro ponto crítico é a baixa tolerância à rotina e tarefas repetitivas, o que pode levar ao desinteresse quando o desafio diminui.',
            'gestao_requerida' => 'Um gestor eficaz para o perfil Dominância deve oferecer autonomia, metas claras e desafios estimulantes. Ele responde melhor a ambientes orientados a resultados do que a ambientes excessivamente normativos. Feedback deve ser direto, objetivo e baseado em desempenho, evitando rodeios ou excesso de formalidade. É importante estabelecer expectativas claras e permitir liberdade na execução. Microgerenciamento tende a gerar frustração e desmotivação. Projetos estratégicos, metas ambiciosas e responsabilidade sobre decisões são fatores que mantêm seu engajamento elevado. Reconhecimento deve estar associado a conquistas concretas.',
            'lideranca' => 'Como líder, você é orientado por metas e desempenho. Conduz equipes com foco em produtividade, rapidez e superação de resultados. Tende a estabelecer objetivos claros e cobrar execução eficiente. É assertivo, toma decisões com segurança e assume responsabilidade pelos riscos. Quando equilibrado, torna-se um líder inspirador pela força, visão estratégica e capacidade de entregar resultados consistentes.',
            'comunicacao' => 'Sua comunicação é direta, objetiva e orientada a ação. Você evita discursos longos e valoriza clareza e eficiência. Prefere conversas produtivas e focadas em solução. Pode ser percebido como firme ou até ríspido, especialmente por perfis mais sensíveis. Sua tendência é cortar excessos e ir ao ponto central da questão. Quando desenvolve empatia e escuta ativa, sua comunicação se torna ainda mais poderosa e estratégica.',
            'ambiente_trabalho' => 'Ambientes dinâmicos, competitivos e orientados a resultados. Preferência por autonomia e liberdade de decisão.',
            'desempenho_tarefas' => 'Executa tarefas com rapidez e foco no resultado final. Prioriza impacto e eficiência acima de detalhes excessivos.',
            'vendas' => 'No contexto comercial, o perfil Dominância tende a adotar abordagem direta e estratégica. Ele identifica rapidamente oportunidades, conduz negociações com objetividade e busca fechamento ágil. Prefere destacar resultados concretos, diferenciais competitivos e benefícios mensuráveis. Sua persuasão é baseada em confiança e autoridade. Pode assumir postura firme em negociações, evitando concessões desnecessárias. Seu ponto forte está na capacidade de conduzir o processo até o fechamento com assertividade e foco em resultados.',
            'motivacao' => 'Desafios, autonomia, metas ambiciosas e reconhecimento por desempenho.',
            'valoriza' => 'Eficiência, competência e capacidade de entrega.',
            'necessidades' => 'Autonomia, poder de decisão e oportunidade de crescimento.',
            'afastamento' => 'Burocracia excessiva, falta de autonomia e ambientes lentos ou indecisos.',
            'busca_resultados' => 'Define metas claras, age rapidamente e ajusta estratégias conforme necessário. Foca no impacto direto das ações.',
            'organizacao' => 'O Executor organiza seu trabalho com foco estratégico, priorizando atividades que gerem maior retorno. Sua abordagem de planejamento é orientada por metas e prazos objetivos. Prefere estruturas simples e funcionais, evitando burocracias que reduzam velocidade. Embora possa não ser excessivamente detalhista, mantém controle sobre indicadores-chave de desempenho.',
            'pressao' => 'Torna-se ainda mais direto e controlador. Pode aumentar o nível de cobrança e urgência.',
            'mudancas' => 'Adapta-se rapidamente quando percebe oportunidade ou vantagem estratégica. Pode resistir apenas quando a mudança reduz sua autonomia.',
            'relacionamentos' => 'Nos relacionamentos interpessoais, tende a assumir postura dominante e decisiva. Valoriza pessoas competentes e produtivas, demonstrando respeito por aqueles que entregam resultados. Pode apresentar baixa tolerância a comportamentos considerados improdutivos. Em conflitos, prefere resolução direta e objetiva. Embora não demonstre sensibilidade excessiva, é leal àqueles que considera comprometidos e confiáveis.',
            'relacionando' => 'Interage de forma assertiva e prática. Busca relações produtivas e objetivas, evitando interações que considere superficiais ou improdutivas.',
            'decisoes' => 'Seu processo decisório é rápido, pragmático e orientado por impacto. Você avalia riscos de forma objetiva e prioriza ação sobre análise prolongada. Costuma decidir com base em viabilidade e resultado esperado. Embora essa agilidade seja uma vantagem competitiva, pode gerar decisões precipitadas se não houver escuta ativa de diferentes perspectivas.',
        ],
        'I' => [
            'nome' => 'Influência',
            'titulo' => 'INFLUÊNCIA (I) - COMUNICADOR',
            'descricao' => 'Você possui um perfil com forte tendência à Influência. Naturalmente social e expansivo, você encontra energia nas interações com outras pessoas e demonstra alto nível de entusiasmo. Gosta de se expressar, de ser ouvido e de deixar uma impressão positiva. Seu jeito comunicativo e otimista faz com que você facilmente estabeleça conexões e inspire outros em torno de você.',
            'subcaracteristicas' => [
                'Sociabilidade' => 'Facilidade em criar conexões, aprecia interação constante e ambientes colaborativos. Extrovertido, aproveita interações sociais e constrói redes amplas.',
                'Entusiasmo' => 'Energia elevada, postura otimista e motivadora. Energia alta, motiva outros e demonstra confiança no sucesso.',
                'Confiança' => 'Segurança nas interações, facilidade para lidar com imprevistos e exposição pública. Acredita no sucesso e lida bem com imprevistos.',
            ],
            'habilidades_basicas' => 'O Comunicador possui habilidade natural para estabelecer rapport rapidamente, influenciar decisões por meio da comunicação e criar redes de relacionamento amplas. Demonstra facilidade em falar em público, apresentar ideias e persuadir com entusiasmo. Sua energia contagiante contribui para ambientes motivadores, estimulando equipes e clientes a aderirem a propostas e projetos com maior engajamento emocional.',
            'habilidades_comuns' => 'Costuma agir de maneira espontânea, comunicativa e otimista. É motivado por reconhecimento, interação e impacto social. Atua com criatividade e flexibilidade, adaptando-se a diferentes públicos com facilidade. Tende a valorizar experiências positivas e ambientes dinâmicos, podendo demonstrar menor interesse por tarefas altamente técnicas ou repetitivas.',
            'vantagens' => 'O perfil Influência apresenta vantagens significativas em contextos que exigem comunicação, negociação e liderança inspiradora. Sua habilidade de gerar conexão interpessoal favorece ambientes colaborativos e fortalece o espírito de equipe. Ele consegue traduzir ideias complexas em mensagens acessíveis, facilitando o alinhamento estratégico dentro de organizações. Na carreira, destaca-se em funções comerciais, marketing, relacionamento com clientes e posições que demandem visibilidade. Sua capacidade de transmitir confiança e entusiasmo aumenta a adesão de pessoas a projetos e decisões. Interpessoalmente, constrói redes amplas e tende a ser percebido como acessível e carismático.',
            'desvantagens' => 'A intensidade social e emocional do Comunicador pode gerar desafios quando não há estrutura adequada. Sua tendência à impulsividade pode levar a decisões baseadas mais em entusiasmo do que em análise aprofundada. Pode iniciar múltiplos projetos simultaneamente sem concluir todos com consistência. A busca por reconhecimento pode influenciar comportamentos voltados à aprovação externa, afetando objetividade em algumas decisões. Pode demonstrar dificuldade em lidar com rotinas rígidas, processos excessivamente técnicos ou ambientes pouco interativos. A comunicação expansiva pode ser interpretada como dispersão ou falta de foco.',
            'gestao_requerida' => 'O Comunicador responde melhor a ambientes colaborativos, com espaço para expressão e criatividade. Um gestor eficaz deve oferecer reconhecimento frequente, feedback positivo e oportunidades de interação social. É importante estabelecer metas claras e acompanhar prazos com estrutura definida, pois pode necessitar de direcionamento para manter foco. Feedback deve ser construtivo e equilibrado, evitando abordagens excessivamente frias ou impessoais. Projetos que envolvam pessoas, comunicação e visibilidade mantêm seu engajamento elevado.',
            'lideranca' => 'Como líder, o perfil Influência é inspirador, carismático e motivador. Conduz equipes por meio de entusiasmo e conexão emocional. Valoriza participação ativa e tende a estimular ideias criativas. Seu ponto forte está na capacidade de engajar e mobilizar pessoas. Contudo, pode precisar desenvolver maior disciplina na cobrança de resultados e acompanhamento de processos. Quando equilibrado, torna-se um líder que combina energia, visão positiva e proximidade com a equipe.',
            'comunicacao' => 'Comunica-se de forma expressiva, envolvente e persuasiva. Utiliza histórias, exemplos e linguagem emocional para criar conexão. Prefere interações presenciais ou dinâmicas e demonstra facilidade em improvisar. Pode falar mais do que ouvir se não houver consciência situacional. Sua comunicação tende a ser otimista e inspiradora, mas pode carecer de detalhamento técnico quando necessário. Desenvolver escuta ativa amplia ainda mais sua eficácia comunicacional.',
            'ambiente_trabalho' => 'Ambientes colaborativos, dinâmicos e interativos. Preferência por locais que valorizem criatividade e comunicação.',
            'desempenho_tarefas' => 'Executa tarefas com energia e entusiasmo. Pode precisar de estrutura para manter constância e organização.',
            'vendas' => 'No contexto comercial, o perfil Influência destaca-se pela capacidade de criar conexão emocional com clientes. Sua abordagem é relacional, baseada em confiança, entusiasmo e storytelling. Ele valoriza o processo de encantamento, construindo vínculos antes de focar exclusivamente no fechamento. Utiliza linguagem persuasiva e demonstra segurança ao apresentar benefícios e diferenciais. Seu carisma facilita quebra de objeções iniciais. Entretanto, pode precisar desenvolver maior disciplina no acompanhamento pós-venda e na formalização de processos.',
            'motivacao' => 'Reconhecimento público, interação social, desafios criativos e ambiente positivo.',
            'valoriza' => 'Entusiasmo, colaboração e capacidade de relacionamento.',
            'necessidades' => 'Interação social, aprovação e liberdade para se expressar.',
            'afastamento' => 'Ambientes excessivamente rígidos, isolamento social e ausência de reconhecimento.',
            'busca_resultados' => 'Mobiliza pessoas, cria engajamento e utiliza influência interpessoal para alcançar metas.',
            'organizacao' => 'O Comunicador tende a organizar-se de maneira flexível, priorizando criatividade e adaptação. Pode estruturar planos iniciais com entusiasmo, mas necessita de disciplina para manter consistência até a conclusão. Prefere métodos visuais e dinâmicos de organização, como quadros, brainstormings e reuniões colaborativas. Sua relação com rotina pode ser variável, demonstrando maior produtividade quando há estímulo externo e interação constante.',
            'pressao' => 'Pode intensificar comunicação e buscar apoio social. Em excesso, pode demonstrar ansiedade ou dispersão.',
            'mudancas' => 'Adapta-se facilmente a mudanças, especialmente quando envolvem novas experiências e interações.',
            'relacionamentos' => 'Nos relacionamentos, demonstra abertura, calor humano e facilidade de aproximação. Constrói redes amplas e valoriza trocas sociais frequentes. É leal quando sente conexão emocional e reconhecimento. Em conflitos, tende a buscar conciliação, mas pode evitar confrontos diretos se perceber risco de rejeição. Sua necessidade de interação constante o leva a investir tempo em manter vínculos ativos.',
            'relacionando' => 'Interage com espontaneidade e energia positiva. Busca ambientes acolhedores e colaborativos, valorizando trocas emocionais e reconhecimento mútuo.',
            'decisoes' => 'O processo decisório do perfil Influência é influenciado por percepção social, entusiasmo e confiança. Pode decidir rapidamente quando motivado, mas nem sempre aprofunda análise técnica. Tende a considerar impacto relacional e aceitação social nas escolhas. Desenvolver maior análise estruturada e avaliação de riscos fortalece sua assertividade estratégica.',
        ],
        'S' => [
            'nome' => 'Estabilidade',
            'titulo' => 'ESTABILIDADE (S) - PLANEJADOR',
            'descricao' => 'Você possui um perfil com forte tendência à Estabilidade. Naturalmente paciente e equilibrado, você valoriza a previsibilidade e se sente confortável em ambientes estruturados e harmoniosos. É leal, confiável e demonstra comprometimento duradouro com aquilo que faz. Seu jeito constante e cooperativo faz com que você seja uma presença estabilizadora para as pessoas ao seu redor.',
            'subcaracteristicas' => [
                'Paciência' => 'Mantém serenidade diante de dificuldades, evita reações impulsivas. Resiliente, estrutura processos.',
                'Consideração' => 'Cuidadoso nas palavras e nas atitudes, atento ao impacto emocional. Cuidadoso e empático.',
                'Persistência' => 'Comprometido com continuidade, lealdade e estabilidade de longo prazo. Esforçado e leal.',
            ],
            'habilidades_basicas' => 'O Planejador possui habilidade natural para manter rotinas organizadas e processos estáveis. Demonstra escuta ativa, empatia e capacidade de apoiar colegas com consistência. Sua postura equilibrada favorece ambientes colaborativos e seguros. Além disso, apresenta grande resistência emocional, sustentando projetos de médio e longo prazo com dedicação constante, mesmo em cenários desafiadores.',
            'habilidades_comuns' => 'Costuma agir de forma estruturada, previsível e cooperativa. É motivado por estabilidade, segurança e reconhecimento pelo comprometimento. Atua com disciplina e busca minimizar riscos antes de agir. Prefere ambientes harmônicos e tende a evitar decisões que possam gerar conflitos desnecessários.',
            'vantagens' => 'O perfil Estabilidade apresenta vantagens significativas em contextos que exigem continuidade, organização e relacionamento consistente. Sua postura paciente permite administrar situações complexas sem perder o equilíbrio emocional. Ele tende a ser visto como confiável, acessível e colaborativo, fortalecendo o clima organizacional. No ambiente profissional, destaca-se pela capacidade de manter processos funcionando de maneira previsível e organizada. É comprometido com prazos e tarefas, demonstrando lealdade à empresa e à equipe. Sua habilidade de escuta o torna um excelente mediador, capaz de compreender diferentes perspectivas antes de agir.',
            'desvantagens' => 'A busca constante por estabilidade pode gerar resistência a mudanças necessárias. O Planejador pode apresentar dificuldade em adaptar-se rapidamente a transformações abruptas ou ambientes altamente competitivos. Sua tendência a evitar conflitos pode resultar em omissão de opiniões importantes ou acúmulo de insatisfações internas. Pode demonstrar lentidão em decisões que exigem rapidez, priorizando análise e segurança. A dificuldade em dizer "não" pode levá-lo a sobrecarga de tarefas. Em excesso, sua necessidade de previsibilidade pode limitar inovação e crescimento acelerado.',
            'gestao_requerida' => 'Um gestor eficaz para o perfil Estabilidade deve oferecer ambiente previsível, metas claras e comunicação respeitosa. Ele responde melhor a orientações estruturadas e processos definidos. Mudanças devem ser apresentadas com antecedência e justificativa clara. Feedback deve ser fornecido de maneira construtiva e privada, valorizando seu comprometimento. É importante reconhecer sua dedicação e lealdade. Tarefas que envolvam continuidade, organização e suporte a equipes são especialmente adequadas.',
            'lideranca' => 'Como líder, o Planejador é colaborativo, paciente e orientado ao bem-estar da equipe. Valoriza relações estáveis e ambiente harmonioso. Conduz pelo exemplo de constância e comprometimento. Pode evitar confrontos diretos, preferindo mediação e consenso. Seu desafio está em desenvolver maior assertividade e rapidez decisória quando o contexto exige firmeza. Quando equilibrado, torna-se um líder confiável e altamente respeitado.',
            'comunicacao' => 'Sua comunicação é calma, ponderada e cuidadosa. Escuta atentamente antes de responder e evita tom agressivo. Prefere diálogos respeitosos e estruturados. Pode demonstrar dificuldade em expressar discordância de forma direta. É percebido como confiável e acolhedor, mas pode precisar desenvolver maior objetividade em situações críticas.',
            'ambiente_trabalho' => 'Ambientes organizados, previsíveis e colaborativos. Preferência por estabilidade e relações de confiança.',
            'desempenho_tarefas' => 'Executa tarefas com constância e disciplina. Prioriza qualidade e continuidade ao invés de velocidade extrema.',
            'vendas' => 'No contexto comercial, o perfil Estabilidade adota abordagem relacional e consultiva. Ele constrói confiança de forma gradual, priorizando relacionamento de longo prazo ao invés de fechamento imediato. Sua escuta ativa facilita identificação precisa das necessidades do cliente. Apresenta produtos ou serviços com clareza e segurança, evitando exageros ou promessas irreais. Pode não demonstrar agressividade comercial, mas compensa com consistência e fidelização. Seu ponto forte está na manutenção de carteira e na construção de vínculos duradouros.',
            'motivacao' => 'Estabilidade, reconhecimento pelo comprometimento e ambiente harmonioso.',
            'valoriza' => 'Lealdade, cooperação e respeito mútuo.',
            'necessidades' => 'Segurança, previsibilidade e relações de confiança.',
            'afastamento' => 'Mudanças abruptas, ambientes instáveis e conflitos frequentes.',
            'busca_resultados' => 'Mantém constância, organização e comprometimento contínuo com metas estabelecidas.',
            'organizacao' => 'O Planejador organiza seu trabalho com estrutura clara e rotina definida. Valoriza planejamento antecipado e cumprimento consistente de prazos. Prefere métodos organizacionais estáveis e processos padronizados. Sua abordagem ao planejamento é cuidadosa e detalhada, buscando reduzir riscos e garantir previsibilidade. Pode evitar improvisos, priorizando segurança e continuidade operacional.',
            'pressao' => 'Pode retrair-se inicialmente, buscando manter equilíbrio. Evita conflitos e tenta preservar estabilidade.',
            'mudancas' => 'Adapta-se gradualmente. Necessita compreender propósito e impacto antes de aceitar transformações.',
            'relacionamentos' => 'Nos relacionamentos, demonstra lealdade, empatia e constância. Valoriza vínculos duradouros e investe em confiança mútua. É um bom ouvinte e tende a apoiar pessoas próximas com dedicação. Evita confrontos diretos, preferindo diálogo conciliador. Pode acumular desconfortos se não expressar necessidades. Sua necessidade de segurança emocional orienta suas escolhas sociais e profissionais.',
            'relacionando' => 'Interage de forma respeitosa e cooperativa. Busca harmonia e evita tensões desnecessárias.',
            'decisoes' => 'O processo decisório do perfil Estabilidade é cauteloso e ponderado. Ele avalia impactos sobre pessoas e processos antes de agir. Prefere segurança a risco elevado. Pode levar mais tempo para decidir, mas tende a manter consistência nas escolhas. Desenvolver maior rapidez e assertividade amplia sua capacidade estratégica.',
        ],
        'C' => [
            'nome' => 'Conformidade',
            'titulo' => 'CONFORMIDADE (C) - ANALISTA',
            'descricao' => 'Você possui um perfil com forte tendência à Conformidade. Naturalmente cuidadoso e reflexivo, você valida informações antes de agir e tem padrões altos de qualidade pessoal. Gosta de ter processos claros, regras bem definidas e clareza sobre o que é esperado. Seu jeito meticuloso e responsável faz com que você seja alguém em quem as pessoas confiam para fazer as coisas da forma correta.',
            'subcaracteristicas' => [
                'Exatidão' => 'Busca precisão, revisa informações múltiplas vezes e evita erros. Cauteloso, odeia erros.',
                'Responsabilidade' => 'Compromisso com normas, processos e padrões estabelecidos. Orientado para regras.',
                'Perceptividade' => 'Atenção a detalhes sutis e capacidade analítica aprofundada. Sensível a sutilezas.',
            ],
            'habilidades_basicas' => 'O Analista possui habilidade natural para organizar informações, estruturar processos e identificar falhas técnicas. Demonstra raciocínio lógico consistente e capacidade de trabalhar com dados complexos. Sua análise criteriosa contribui para tomadas de decisão mais seguras e fundamentadas. Além disso, apresenta disciplina intelectual e compromisso com qualidade, sendo altamente confiável em atividades que exigem precisão e controle.',
            'habilidades_comuns' => 'Costuma agir com cautela, baseando-se em fatos e evidências. É motivado por clareza, organização e padronização. Atua de forma metódica, evitando improvisos e decisões precipitadas. Prefere ambientes estruturados, com regras bem definidas e expectativas claras.',
            'vantagens' => 'O perfil Conformidade apresenta vantagens significativas em contextos que exigem precisão técnica, análise estratégica e controle de qualidade. Sua capacidade de identificar inconsistências reduz riscos operacionais e aumenta a confiabilidade dos processos. Ele contribui para tomada de decisões fundamentadas, evitando erros decorrentes de impulsividade ou superficialidade. Na carreira, destaca-se em funções analíticas, técnicas, jurídicas, financeiras ou científicas. Demonstra comprometimento com excelência e padrões elevados. Interpessoalmente, transmite seriedade e profissionalismo, sendo percebido como responsável e confiável.',
            'desvantagens' => 'A busca constante por perfeição pode gerar lentidão excessiva na execução. O Analista pode adiar decisões enquanto busca informações adicionais, dificultando agilidade em ambientes dinâmicos. Sua rigidez com normas pode ser percebida como inflexibilidade. Pode demonstrar dificuldade em lidar com ambiguidade ou improvisação. Em situações de pressão, pode tornar-se excessivamente crítico ou retraído. Sua comunicação objetiva pode parecer fria ou distante para perfis mais emocionais. Além disso, pode apresentar resistência a mudanças que não estejam bem estruturadas ou fundamentadas.',
            'gestao_requerida' => 'Um gestor eficaz para o perfil Conformidade deve fornecer instruções claras, objetivos bem definidos e critérios mensuráveis. Ele responde melhor a ambientes organizados e com processos estruturados. Feedback deve ser baseado em dados concretos e argumentos lógicos. Mudanças devem ser comunicadas com antecedência e fundamentação técnica. Tarefas que envolvam análise, planejamento estratégico e controle de qualidade são especialmente adequadas.',
            'lideranca' => 'Como líder, o Analista é estruturado, criterioso e orientado à qualidade. Estabelece padrões elevados e espera precisão da equipe. Conduz por meio de organização e fundamentação técnica. Pode demonstrar menor ênfase em aspectos emocionais da liderança, priorizando eficiência e correção. Seu desafio está em desenvolver maior flexibilidade interpessoal e reconhecimento emocional da equipe. Quando equilibrado, torna-se um líder estratégico e altamente confiável.',
            'comunicacao' => 'Sua comunicação é objetiva, lógica e detalhada. Prefere dados e argumentos estruturados ao invés de apelos emocionais. Evita exageros e tende a falar apenas quando possui segurança sobre o tema. Pode ser percebido como reservado ou crítico. Desenvolver maior expressividade emocional pode ampliar sua influência interpessoal.',
            'ambiente_trabalho' => 'Ambientes organizados, estruturados e orientados a qualidade. Preferência por regras claras e expectativas definidas.',
            'desempenho_tarefas' => 'Executa tarefas com precisão e atenção aos detalhes. Prioriza qualidade técnica acima de velocidade.',
            'vendas' => 'No contexto comercial, o perfil Conformidade adota abordagem consultiva e baseada em dados. Ele apresenta produtos ou serviços com fundamentação técnica detalhada, destacando evidências, especificações e diferenciais concretos. Sua credibilidade é construída por meio de conhecimento aprofundado e precisão nas informações. Pode não utilizar apelos emocionais intensos, mas transmite segurança e profissionalismo. Seu ponto forte está na venda técnica e na construção de confiança baseada em competência.',
            'motivacao' => 'Precisão, reconhecimento por competência técnica e ambiente estruturado.',
            'valoriza' => 'Responsabilidade, competência e confiabilidade.',
            'necessidades' => 'Clareza, organização e padrões definidos.',
            'afastamento' => 'Ambientes caóticos, falta de critérios claros e decisões impulsivas.',
            'busca_resultados' => 'Analisa profundamente, estrutura processos e reduz riscos antes de agir.',
            'organizacao' => 'O Analista organiza seu trabalho com métodos detalhados e planejamento estruturado. Utiliza listas, cronogramas e indicadores claros para monitorar progresso. Prefere antecipar riscos e criar planos preventivos. Sua abordagem é sistemática e orientada por dados. Valoriza consistência e previsibilidade. Pode dedicar tempo significativo ao planejamento antes da execução, buscando minimizar erros e retrabalhos.',
            'pressao' => 'Pode tornar-se mais crítico e reservado. Busca controle por meio de análise adicional.',
            'mudancas' => 'Aceita mudanças quando bem fundamentadas e estruturadas. Resiste a transformações abruptas ou mal planejadas.',
            'relacionamentos' => 'Nos relacionamentos, tende a ser reservado, seletivo e confiável. Valoriza profundidade ao invés de quantidade de conexões. Demonstra lealdade quando estabelece confiança. Pode apresentar dificuldade em expressar emoções abertamente. Prefere interações baseadas em respeito mútuo e competência técnica. Em conflitos, utiliza argumentos racionais para resolver divergências.',
            'relacionando' => 'Interage com seriedade e objetividade. Busca relações baseadas em confiança, competência e previsibilidade.',
            'decisoes' => 'O processo decisório do perfil Conformidade é analítico e fundamentado. Ele coleta dados, avalia riscos e considera múltiplos cenários antes de decidir. Pode levar mais tempo para concluir decisões importantes, priorizando segurança e precisão. Desenvolver maior agilidade estratégica amplia sua eficácia em contextos dinâmicos.',
        ],
    ];

    public function generate(array $summary): array
    {
        $counts = $summary['counts'] ?? [];
        $perc = $summary['perc'] ?? [];
        $ordered = $summary['ordered'] ?? [];

        $reportSections = $this->generateSections($perc);

        return [
            'summary' => ['perc' => $perc, 'counts' => $counts, 'ordered' => $ordered],
            'sections' => $reportSections,
        ];
    }

    private function generateSections(array $perc): array
    {
        $result = [];
        $profileLetters = ['D' => 'Dominância', 'I' => 'Influência', 'S' => 'Estabilidade', 'C' => 'Conformidade'];

        foreach ($profileLetters as $letter => $name) {
            $percentage = $perc[$letter] ?? 0;
            $classification = $this->classifyPercentage($percentage);
            $data = $this->profilesData[$letter];

            $result[$name] = [
                'nome' => $data['nome'],
                'titulo' => $data['titulo'],
                'percentagem' => $percentage,
                'classificacao' => $classification,
            ] + $data;
        }

        return $result;
    }

    private function classifyPercentage(int $percentage): string
    {
        if ($percentage >= 35) return 'MUITO ALTO';
        if ($percentage >= 28) return 'ALTO';
        if ($percentage >= 23) return 'NORMAL ALTO';
        if ($percentage >= 18) return 'MÉDIO/EQUILIBRADO';
        if ($percentage >= 13) return 'NORMAL BAIXO';
        if ($percentage >= 8) return 'BAIXO';
        return 'MUITO BAIXO';
    }

    private function getLogoBase64(): string
    {
        $logoPath = public_path('images/logo.png');
        
        if (file_exists($logoPath)) {
            $imageData = file_get_contents($logoPath);
            $base64 = base64_encode($imageData);
            return 'data:image/png;base64,' . $base64;
        }
        
        return 'data:image/svg+xml;base64,' . base64_encode('<svg width="50" height="50" viewBox="0 0 50 50"><rect width="50" height="50" fill="#0f172a"/><text x="25" y="32" font-size="24" font-weight="bold" fill="white" text-anchor="middle" font-family="Arial">MD</text></svg>');
    }

    public function generateHTML(array $report, ?string $userName = null, ?string $userEmail = null, ?int $responseTimeMinutes = null): string
    {
        $logoBase64 = $this->getLogoBase64();

        $colorMap = [
            'Dominância' => '#ef4444',
            'Influência' => '#f59e0b',
            'Estabilidade' => '#22c55e',
            'Conformidade' => '#3b82f6',
        ];

        $classMap = [
            'Dominância' => 'dominancia',
            'Influência' => 'influencia',
            'Estabilidade' => 'estabilidade',
            'Conformidade' => 'conformidade',
        ];

        $sections = $report['sections'];
        uasort($sections, fn($a, $b) => $b['percentagem'] - $a['percentagem']);

        $dominantProfile = array_key_first($sections);
        $dominantData = $sections[$dominantProfile];

        preg_match('/- (.+)$/', $dominantData['titulo'], $matches);
        $profileSubtitle = $matches[1] ?? $dominantProfile;

        $testDate = date('d \\d\\e F \\d\\e Y', strtotime('now'));
        $meses = ['January' => 'janeiro', 'February' => 'fevereiro', 'March' => 'março', 'April' => 'abril', 
                  'May' => 'maio', 'June' => 'junho', 'July' => 'julho', 'August' => 'agosto', 
                  'September' => 'setembro', 'October' => 'outubro', 'November' => 'novembro', 'December' => 'dezembro'];
        foreach ($meses as $en => $pt) {
            $testDate = str_replace($en, $pt, $testDate);
        }

        $userNameDisplay = !empty($userName) ? htmlspecialchars($userName) : '';

        $html = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório DISC - MD1 Academy</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background: #f5f5f5; }
        .page { page-break-after: always; min-height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center; background: white; padding: 40px; }
        .page-break { page-break-after: always; }
        
        .cover { text-align: center; position: relative; }
        .cover-logo { position: absolute; top: 30px; right: 30px; }
        .cover-logo img { width: 100px; height: auto; }
        .cover-header { margin-bottom: 60px; margin-top: 40px; font-size: 12px; text-transform: uppercase; color: #666; font-weight: 600; letter-spacing: 2px; }
        .cover-title { font-size: 48px; font-weight: 800; color: #0f172a; margin: 80px 0 40px 0; line-height: 1.2; }
        .cover-subtitle { font-size: 16px; color: #9ca3af; margin: 20px 0; }
        .cover-name { font-size: 24px; font-weight: 600; color: #0f172a; margin: 60px 0 20px 0; }
        .cover-footer { position: absolute; bottom: 30px; left: 0; right: 0; font-size: 11px; color: #999; }
        
        .back-cover { text-align: center; position: relative; }
        .back-cover-logo { position: absolute; top: 30px; right: 30px; }
        .back-cover-logo img { width: 100px; height: auto; }
        .back-cover-header { margin-bottom: 60px; margin-top: 40px; font-size: 12px; text-transform: uppercase; color: #666; font-weight: 600; letter-spacing: 2px; }
        .back-cover-title { font-size: 48px; font-weight: 800; color: #0f172a; margin: 80px 0 60px 0; line-height: 1.2; }
        .back-cover-content { text-align: left; max-width: 500px; margin: 0 auto; background: #f9fafb; padding: 30px; border-radius: 8px; }
        .back-cover-item { margin: 15px 0; font-size: 14px; line-height: 1.6; }
        .back-cover-item strong { color: #0f172a; font-weight: 600; display: block; margin-bottom: 4px; }
        .back-cover-item span { color: #666; }
        .back-cover-footer { position: absolute; bottom: 30px; left: 0; right: 0; font-size: 11px; color: #999; }
        
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 40px; }
        .header { text-align: center; border-bottom: 3px solid #0f172a; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #0f172a; font-size: 28px; }
        .header p { margin: 5px 0; color: #666; font-size: 14px; }
        
        .profile-intro { background: #f0f4ff; padding: 20px; border-radius: 8px; border-left: 5px solid #3b82f6; margin-bottom: 30px; }
        .profile-intro p { margin: 0; font-size: 14px; color: #0f172a; line-height: 1.6; }
        
        .summary-bars { margin-bottom: 30px; }
        .bar-row { display: flex; align-items: center; margin-bottom: 20px; gap: 15px; }
        .bar-label { font-weight: bold; color: #0f172a; min-width: 120px; font-size: 14px; }
        .bar-container { flex: 1; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden; }
        .bar-fill { height: 100%; transition: width 0.3s ease; border-radius: 4px; }
        .bar-stats { font-weight: bold; color: white; background: #333; padding: 4px 12px; border-radius: 4px; min-width: 45px; text-align: center; font-size: 13px; }
        .bar-count { font-size: 12px; color: #666; min-width: 140px; text-align: right; }
        
        .section { page-break-inside: avoid; margin-bottom: 40px; border-left: 5px solid; padding-left: 20px; }
        .section.dominancia { border-left-color: #ef4444; }
        .section.influencia { border-left-color: #f59e0b; }
        .section.estabilidade { border-left-color: #22c55e; }
        .section.conformidade { border-left-color: #3b82f6; }
        .section-title { font-size: 20px; font-weight: bold; margin: 0 0 10px 0; color: #0f172a; }
        .intensity { font-size: 13px; color: #666; margin-bottom: 15px; font-weight: bold; }
        .subsection-title { font-size: 14px; font-weight: bold; margin-top: 15px; margin-bottom: 8px; color: #0f172a; }
        .text-block { margin-bottom: 15px; text-align: justify; font-size: 13px; line-height: 1.5; }
        .subchar-list { margin: 10px 0; padding-left: 20px; }
        .subchar-item { margin: 8px 0; font-size: 13px; }
        .subchar-item strong { color: #0f172a; }
        ul { margin: 8px 0; padding-left: 20px; }
        li { margin: 4px 0; font-size: 13px; }
        .footer { text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #999; font-size: 12px; }
        @media print { body { background: white; } }
    </style>
</head>
<body>';

        $html .= '
    <div class="page cover">
        <div class="cover-logo">
            <img src="' . $logoBase64 . '" alt="MD1 Academy" />
        </div>
        <div class="cover-header">V J L MORGADO - MARKETING DIGITAL E<br>COMERCIO ON-LINE</div>
        <h1 class="cover-title">RELATÓRIO<br>COMPORTAMENTAL</h1>';

        if (!empty($userName)) {
            $html .= '<div class="cover-name">' . $userNameDisplay . '</div>';
        }

        $html .= '<div class="cover-subtitle">FEEDBACK</div>
        <div class="cover-footer"></div>
    </div>';

        $html .= '
    <div class="page-break"></div>
    <div class="page back-cover">
        <div class="back-cover-logo">
            <img src="' . $logoBase64 . '" alt="MD1 Academy" />
        </div>
        <div class="back-cover-header">V J L MORGADO - MARKETING DIGITAL E<br>COMERCIO ON-LINE</div>
        <h1 class="back-cover-title">Relatório<br>Comportamental</h1>
        <div class="back-cover-content">';

        if (!empty($userName)) {
            $html .= '<div class="back-cover-item"><strong style="color: #0f172a; font-size: 16px;">' . $userNameDisplay . '</strong></div>';
        }

        if (!empty($userEmail)) {
            $html .= '<div class="back-cover-item"><strong>Email:</strong> <span>' . htmlspecialchars($userEmail) . '</span></div>';
        }

        if (!empty($responseTimeMinutes)) {
            $html .= '<div class="back-cover-item"><strong>Tempo percorrido de teste:</strong> <span>' . intval($responseTimeMinutes) . ' minutos</span></div>';
        }

        $html .= '<div class="back-cover-item"><strong>Profiler realizado em:</strong> <span>' . $testDate . '</span></div>';

        $html .= '
        </div>
        <div class="back-cover-footer"></div>
    </div>';

        $html .= '
    <div class="page-break"></div>
    <div class="container">';

        if (!empty($userName)) {
            $html .= '
    <div class="profile-intro">
        <p><strong>Atualmente o sistema projeta:</strong></p>
        <p>O perfil de <strong>' . $userNameDisplay . '</strong> é de <strong>' . $dominantProfile . '</strong>, também conhecido como <strong>' . htmlspecialchars($profileSubtitle) . '</strong>.</p>
    </div>';
        }

        $html .= '<h2 style="color: #0f172a; border-bottom: 2px solid #0f172a; padding-bottom: 10px;">Resumo do Perfil</h2>';
        $html .= '<div class="summary-bars">';
        
        $counts = $report['summary']['counts'] ?? [];
        $profileLetters = ['Dominância' => 'D', 'Influência' => 'I', 'Estabilidade' => 'S', 'Conformidade' => 'C'];
        
        foreach ($sections as $profile => $data) {
            $percentage = $data['percentagem'];
            $count = $counts[$profile[0]] ?? 0;
            $color = $colorMap[$profile] ?? '#999';
            $letter = $profileLetters[$profile] ?? '';
            
            $html .= '<div class="bar-row">
                <div class="bar-label">' . $letter . ' - ' . htmlspecialchars($profile) . '</div>
                <div class="bar-container">
                    <div class="bar-fill" style="width: ' . $percentage . '%; background-color: ' . $color . ';"></div>
                </div>
                <div class="bar-stats"><strong>' . $percentage . '%</strong></div>
                <div class="bar-count">' . intval($count) . ' respostas (' . $percentage . '%)</div>
            </div>';
        }
        
        $html .= '</div>';

        foreach ($sections as $profile => $data) {
            $sectionClass = $classMap[$profile] ?? '';
            $html .= '<div class="section ' . $sectionClass . '">';
            
            if (!empty($userName)) {
                $html .= '<h2 class="section-title">O Perfil ' . $userNameDisplay . ': ' . htmlspecialchars($data['titulo']) . '</h2>';
            } else {
                $html .= '<h2 class="section-title">' . htmlspecialchars($data['titulo']) . '</h2>';
            }
            
            $html .= '<div class="intensity">Intensidade: ' . $data['percentagem'] . '% - ' . $data['classificacao'] . '</div>';
            $html .= '<div class="text-block" style="margin-top: 15px; margin-bottom: 20px; font-size: 14px;">' . htmlspecialchars($data['descricao']) . '</div>';

            $html .= '<div class="subsection-title">Subcaracterísticas</div>';
            $html .= '<div class="subchar-list">';
            foreach ($data['subcaracteristicas'] as $sub => $desc) {
                $html .= '<div class="subchar-item"><strong>' . htmlspecialchars($sub) . ':</strong> ' . htmlspecialchars($desc) . '</div>';
            }
            $html .= '</div>';

            $html .= '<div class="subsection-title">Habilidades Básicas</div>';
            $html .= '<div class="text-block">' . htmlspecialchars($data['habilidades_basicas']) . '</div>';

            $html .= '<div class="subsection-title">Como Atua</div>';
            $html .= '<div class="text-block">' . htmlspecialchars($data['habilidades_comuns']) . '</div>';

            $html .= '<div class="subsection-title">Vantagens</div>';
            $html .= '<div class="text-block">' . htmlspecialchars($data['vantagens']) . '</div>';

            $html .= '<div class="subsection-title">Áreas de Desenvolvimento</div>';
            $html .= '<div class="text-block">' . htmlspecialchars($data['desvantagens']) . '</div>';

            $html .= '<div class="subsection-title">Estilo de Gestão Ideal</div>';
            $html .= '<div class="text-block">' . htmlspecialchars($data['gestao_requerida']) . '</div>';

            $html .= '<div class="subsection-title">Liderança</div>';
            $html .= '<div class="text-block">' . htmlspecialchars($data['lideranca']) . '</div>';

            $html .= '<div class="subsection-title">Comunicação</div>';
            $html .= '<div class="text-block">' . htmlspecialchars($data['comunicacao']) . '</div>';

            $html .= '<div class="subsection-title">Ambiente Ideal</div>';
            $html .= '<div class="text-block">' . htmlspecialchars($data['ambiente_trabalho']) . '</div>';

            $html .= '<div class="subsection-title">Desempenho em Tarefas</div>';
            $html .= '<div class="text-block">' . htmlspecialchars($data['desempenho_tarefas']) . '</div>';

            $html .= '<div class="subsection-title">Estilo de Vendas</div>';
            $html .= '<div class="text-block">' . htmlspecialchars($data['vendas']) . '</div>';

            $html .= '<div class="subsection-title">O Que o Motiva</div>';
            $html .= '<div class="text-block">' . htmlspecialchars($data['motivacao']) . '</div>';

            $html .= '<div class="subsection-title">O Que Valoriza nos Outros</div>';
            $html .= '<div class="text-block">' . htmlspecialchars($data['valoriza']) . '</div>';

            $html .= '<div class="subsection-title">Necessidades Fundamentais</div>';
            $html .= '<div class="text-block">' . htmlspecialchars($data['necessidades']) . '</div>';

            $html .= '<div class="subsection-title">O Que o Afasta</div>';
            $html .= '<div class="text-block">' . htmlspecialchars($data['afastamento']) . '</div>';

            $html .= '<div class="subsection-title">Busca de Resultados</div>';
            $html .= '<div class="text-block">' . htmlspecialchars($data['busca_resultados']) . '</div>';

            $html .= '<div class="subsection-title">Organização e Planejamento</div>';
            $html .= '<div class="text-block">' . htmlspecialchars($data['organizacao']) . '</div>';

            $html .= '<div class="subsection-title">Reação Sob Pressão</div>';
            $html .= '<div class="text-block">' . htmlspecialchars($data['pressao']) . '</div>';

            $html .= '<div class="subsection-title">Relação Com Mudanças</div>';
            $html .= '<div class="text-block">' . htmlspecialchars($data['mudancas']) . '</div>';

            $html .= '<div class="subsection-title">Relacionamentos Interpessoais</div>';
            $html .= '<div class="text-block">' . htmlspecialchars($data['relacionamentos']) . '</div>';

            $html .= '<div class="subsection-title">Como Se Relaciona Com os Outros</div>';
            $html .= '<div class="text-block">' . htmlspecialchars($data['relacionando']) . '</div>';

            $html .= '<div class="subsection-title">Processo Decisório</div>';
            $html .= '<div class="text-block">' . htmlspecialchars($data['decisoes']) . '</div>';

            $html .= '</div>';
        }

        $html .= '<div class="footer">
            <p>Relatório gerado automaticamente - MD1 Academy</p>
            <p>Use este documento para autoconhecimento e desenvolvimento profissional</p>
        </div>';

        $html .= '    </div></body></html>';

        return $html;
    }}

