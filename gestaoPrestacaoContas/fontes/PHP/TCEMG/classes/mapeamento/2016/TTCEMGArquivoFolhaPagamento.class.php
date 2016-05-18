<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Classe de mapeamento do arquivo FLPGO.CSV
    * Data de Criação:  23/03/2016

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Evandro Melos

    * @package URBEM
    * @subpackage Mapeamento

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGArquivoFolhaPagamento extends Persistente
{
    public function __construct()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaDadosExportacaoFolhaPagamento10(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaDadosExportacaoFolhaPagamento10();
        $this->stDebug = $stSql;        
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    } 
    
    public function montaRecuperaDadosExportacaoFolhaPagamento10()
    {
        $inCodPeriodoMovimentacao = $this->getDado('cod_periodo_movimentacao');
        $stDataInicialPeriodo = $this->getDado('data_inicial_periodo');        
        $stDataFinalPeriodo = $this->getDado('data_final_periodo');
        $stExercicio = $this->getDado('exercicio');
        $stEntidade = Sessao::getEntidade();
        $inCodEntidade = $this->getDado('cod_entidade');
        
        $stSql = "  SELECT * FROM (
                        SELECT DISTINCT
                            
                            10 as tipo_registro
                            ,sw_cgm_pessoa_fisica.cpf AS num_cpf
                            ,CASE WHEN remuneracoes.tipo_calculo = 'M' THEN
                                    contrato.registro||''||1
                                 WHEN remuneracoes.tipo_calculo = 'D' THEN
                                    contrato.registro||''||2
                                 WHEN remuneracoes.tipo_calculo = 'E' THEN
                                    contrato.registro||''||3
                            END as cod_reduzido_pessoa
                            ,'C' AS regime
                            ,remuneracoes.tipo_calculo as tipo_pagamento
                            ,CASE WHEN aposentadoria.cod_contrato IS NULL THEN
                                    'A'
                               ELSE
                                    'I'
                            END AS situacao_servidor_pensionista
                            ,'' as descricao_situacao
                            ,TO_CHAR(aposentadoria.dt_concessao,'ddmmyyyy') as data_concessao_aposentadoria_pensao
                            ,remove_acentos(cargo.descricao) as nome_cargo
                            ,SUBSTR(tcemg_entidade_cargo_servidor.descricao,1,3) as sigla_cargo
                            ,SUBSTR(tcemg_entidade_cargo_servidor.descricao,7) AS descricao_sigla_cargo
                            ,COALESCE(tcemg_entidade_requisitos_cargo.cod_tipo,4) as requisito_cargo
                            , CASE WHEN adido_cedido.tipo_cedencia = 'c' AND indicativo_onus = 'c'
                                    THEN 'SCO'
                                    WHEN adido_cedido.tipo_cedencia = 'c' AND indicativo_onus = 'e'
                                    THEN 'SCS'
                                    ELSE ''
                            END AS indicador_cessao
                            ,orgao_descricao.descricao as nome_lotacao
                            ,ultimo_contrato_servidor_salario.horas_semanais::VARCHAR as valor_horas_semanais
                            ,TO_CHAR(contrato_servidor_funcao.vigencia,'ddmmyyyy') as data_efetivacao_exercicio_cargo
                            ,CASE WHEN aposentadoria_encerramento.cod_contrato IS NOT NULL 
                                    THEN TO_CHAR(aposentadoria_encerramento.dt_encerramento,'ddmmyyyy')                
                                    ELSE TO_CHAR(ultimo_contrato_servidor_caso_causa.dt_rescisao,'ddmmyyyy')
                            END AS data_exclusao                            
                            ,COALESCE(remuneracoes.valor,0.00) as valor_remuneracao_bruto
                            ,CASE WHEN (remuneracoes.valor - COALESCE(irrf_previdencia.valor,0.00)) >= 0 THEN
                                    'C'
                                    ELSE
                                    'D'
                            END as natureza_saldo_liquido
                            ,((remuneracoes.valor - COALESCE(irrf_previdencia.valor,0.00))) as valor_remuneracao_liquida
                            ,COALESCE(irrf_previdencia.valor,0.00) as valor_obrigacoes
                                              
                    
                            FROM pessoal.contrato
                    
                            INNER JOIN folhapagamento.contrato_servidor_periodo
                                    ON contrato_servidor_periodo.cod_contrato = contrato.cod_contrato
                                    AND contrato_servidor_periodo.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao."
                                    
                            INNER JOIN pessoal.contrato_servidor
                                    ON contrato_servidor.cod_contrato = contrato.cod_contrato
                    
                            INNER JOIN pessoal.servidor_contrato_servidor
                                    ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato
                    
                            INNER JOIN pessoal.servidor
                                    ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
                    
                            INNER JOIN sw_cgm_pessoa_fisica
                                    ON sw_cgm_pessoa_fisica.numcgm = servidor.numcgm
                            
                            INNER JOIN ultimo_contrato_servidor_funcao('".$stEntidade."', '".$inCodPeriodoMovimentacao."') as ultimo_contrato_servidor_funcao
                                    ON contrato_servidor_periodo.cod_contrato = ultimo_contrato_servidor_funcao.cod_contrato
                            
                            INNER JOIN ( SELECT contrato_servidor_funcao.*
                                            FROM pessoal.contrato_servidor_funcao
                                            INNER JOIN ( SELECT cod_contrato
                                                                ,cod_cargo
                                                                ,MAX(timestamp) as timestamp
                                                          from pessoal.contrato_servidor_funcao
                                                          WHERE timestamp <= ultimoTimestampPeriodoMovimentacao(".$inCodPeriodoMovimentacao.",'".$stEntidade."')::TIMESTAMP
                                                          group by 1,2
                                                    ) as max
                                                    ON max.cod_contrato     = contrato_servidor_funcao.cod_contrato
                                                    AND max.cod_cargo       = contrato_servidor_funcao.cod_cargo
                                                    AND max.timestamp       = contrato_servidor_funcao.timestamp
                                    ) as contrato_servidor_funcao
                                    ON contrato_servidor_funcao.cod_contrato = ultimo_contrato_servidor_funcao.cod_contrato
                                    AND contrato_servidor_funcao.cod_cargo = ultimo_contrato_servidor_funcao.cod_cargo                
                    
                    
                            INNER JOIN pessoal.cargo
                                    ON cargo.cod_cargo = contrato_servidor_funcao.cod_cargo
                    
                            INNER JOIN ultimo_contrato_servidor_sub_divisao_funcao('".$stEntidade."', '".$inCodPeriodoMovimentacao."') as ultimo_contrato_servidor_sub_divisao_funcao
                                    ON contrato_servidor_periodo.cod_contrato = ultimo_contrato_servidor_sub_divisao_funcao.cod_contrato
                    
                            INNER JOIN ( SELECT cargo_sub_divisao.*
                                            FROM pessoal.cargo_sub_divisao
                                            INNER JOIN( SELECT cargo_sub_divisao.cod_cargo
                                                               ,cargo_sub_divisao.cod_sub_divisao
                                                            , max(cargo_sub_divisao.timestamp) as timestamp
                                                            FROM pessoal.cargo_sub_divisao
                                                            WHERE cargo_sub_divisao.timestamp <= ultimoTimestampPeriodoMovimentacao(".$inCodPeriodoMovimentacao.",'".$stEntidade."')::TIMESTAMP
                                                            GROUP BY cod_cargo,cod_sub_divisao
                                                    )as max_cargo_sub_divisao
                                                    ON cargo_sub_divisao.cod_cargo = max_cargo_sub_divisao.cod_cargo
                                                    AND cargo_sub_divisao.cod_sub_divisao = max_cargo_sub_divisao.cod_sub_divisao
                                                    AND cargo_sub_divisao.timestamp = max_cargo_sub_divisao.timestamp
                                        ) as cargo_sub_divisao
                                    ON cargo_sub_divisao.cod_cargo = contrato_servidor_funcao.cod_cargo
                                    AND cargo_sub_divisao.cod_sub_divisao = ultimo_contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao
                                    
                            LEFT JOIN ( SELECT 
                                            tipo_cargo_servidor.descricao
                                            ,tcemg_entidade_cargo_servidor.cod_cargo
                                            ,tcemg_entidade_cargo_servidor.cod_sub_divisao
                            
                                            from folhapagamento.tcemg_entidade_cargo_servidor
                                            INNER JOIN tcemg.tipo_cargo_servidor
                                                    ON tipo_cargo_servidor.cod_tipo = tcemg_entidade_cargo_servidor.cod_tipo
                                            INNER JOIN pessoal.sub_divisao
                                                    ON sub_divisao.cod_sub_divisao = tcemg_entidade_cargo_servidor.cod_sub_divisao
                                            INNER JOIN pessoal.cargo
                                                    ON cargo.cod_cargo = tcemg_entidade_cargo_servidor.cod_cargo
                                            WHERE tcemg_entidade_cargo_servidor.exercicio = '".Sessao::getExercicio()."'
                                    ) as tcemg_entidade_cargo_servidor
                                    ON tcemg_entidade_cargo_servidor.cod_cargo = cargo_sub_divisao.cod_cargo
                                    AND tcemg_entidade_cargo_servidor.cod_sub_divisao = cargo_sub_divisao.cod_sub_divisao
                    
                            LEFT JOIN ( SELECT 
                                            tipo_requisitos_cargo.descricao
                                            ,tcemg_entidade_requisitos_cargo.cod_tipo
                                            ,tcemg_entidade_requisitos_cargo.cod_cargo                
                                            from folhapagamento.tcemg_entidade_requisitos_cargo
                                            INNER JOIN tcemg.tipo_requisitos_cargo
                                                    ON tipo_requisitos_cargo.cod_tipo = tcemg_entidade_requisitos_cargo.cod_tipo
                                            INNER JOIN pessoal.cargo
                                                    ON cargo.cod_cargo = tcemg_entidade_requisitos_cargo.cod_cargo
                                            WHERE tcemg_entidade_requisitos_cargo.exercicio = '".Sessao::getExercicio()."'
                                    ) as tcemg_entidade_requisitos_cargo
                                    ON tcemg_entidade_requisitos_cargo.cod_cargo = contrato_servidor_funcao.cod_cargo
                    
                            LEFT JOIN ( SELECT aposentadoria.*
                                            FROM pessoal.aposentadoria
                                            INNER JOIN ( SELECT cod_contrato
                                                                ,max(timestamp) as timestamp
                                                            FROM pessoal.aposentadoria
                                                            WHERE timestamp <= ultimoTimestampPeriodoMovimentacao(".$inCodPeriodoMovimentacao.",'".$stEntidade."')::TIMESTAMP
                                                            GROUP BY 1
                                            ) as max
                                            ON max.cod_contrato     = aposentadoria.cod_contrato
                                            AND max.timestamp       = aposentadoria.timestamp
                                    ) AS aposentadoria
                                    ON aposentadoria.cod_contrato = contrato_servidor_periodo.cod_contrato
                    
                            LEFT JOIN (SELECT aposentadoria_encerramento.*
                                            FROM pessoal.aposentadoria_encerramento
                                            INNER JOIN ( SELECT aposentadoria_encerramento.cod_contrato
                                                                ,MAX(aposentadoria_encerramento.timestamp) as timestamp
                                                         FROM pessoal.aposentadoria_encerramento
                                                         WHERE timestamp <= ultimoTimestampPeriodoMovimentacao(".$inCodPeriodoMovimentacao.",'".$stEntidade."')::TIMESTAMP
                                                         Group BY 1
                                            ) as max
                                            ON max.cod_contrato     = aposentadoria_encerramento.cod_contrato
                                            AND max.timestamp       = aposentadoria_encerramento.timestamp
                                    ) as aposentadoria_encerramento
                                    ON aposentadoria_encerramento.cod_contrato = aposentadoria.cod_contrato
                                    AND aposentadoria_encerramento.timestamp = aposentadoria.timestamp
                    
                            LEFT JOIN ( SELECT adido_cedido.*
                                            FROM pessoal.adido_cedido
                                            LEFT JOIN pessoal.adido_cedido_excluido
                                                    ON adido_cedido_excluido.cod_contrato            = adido_cedido.cod_contrato
                                                    AND adido_cedido_excluido.cod_norma              = adido_cedido.cod_norma
                                                    AND adido_cedido_excluido.timestamp_cedido_adido = adido_cedido.timestamp
                                            WHERE adido_cedido_excluido.cod_contrato IS NOT NULL
                                            AND adido_cedido.timestamp between '".$stDataInicialPeriodo."' AND '".$stDataFinalPeriodo."'
                                    ) as adido_cedido
                                    ON adido_cedido.cod_contrato = contrato_servidor.cod_contrato
                    
                            INNER JOIN ultimo_contrato_servidor_orgao('".$stEntidade."', '".$inCodPeriodoMovimentacao."') as ultimo_contrato_servidor_orgao
                                    ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_orgao.cod_contrato
                            
                            INNER JOIN (SELECT orgao_descricao.cod_orgao
                                                ,orgao_descricao.descricao
                                                ,orgao_descricao.timestamp
                                                FROM organograma.orgao_descricao
                                                INNER JOIN( SELECT cod_orgao
                                                                   ,MAX(timestamp) as timestamp
                                                                FROM organograma.orgao_descricao
                                                                WHERE timestamp <= ultimoTimestampPeriodoMovimentacao(".$inCodPeriodoMovimentacao.",'".$stEntidade."')::TIMESTAMP
                                                                group BY 1
                                                )as max 
                                                        ON max.cod_orgao = orgao_descricao.cod_orgao
                                                        AND max.timestamp = orgao_descricao.timestamp                                                
                                    ) as orgao_descricao
                                ON orgao_descricao.cod_orgao = ultimo_contrato_servidor_orgao.cod_orgao
                                AND orgao_descricao.timestamp <= ultimoTimestampPeriodoMovimentacao(".$inCodPeriodoMovimentacao.",'".$stEntidade."')::TIMESTAMP
                    
                            INNER JOIN ultimo_contrato_servidor_salario('".$stEntidade."', '".$inCodPeriodoMovimentacao."') as ultimo_contrato_servidor_salario
                                    ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_salario.cod_contrato
                    
                            LEFT JOIN ultimo_contrato_servidor_caso_causa('".$stEntidade."', '".$inCodPeriodoMovimentacao."') as ultimo_contrato_servidor_caso_causa
                                    ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_caso_causa.cod_contrato
                                    AND ultimo_contrato_servidor_caso_causa.dt_rescisao <= '".$stDataFinalPeriodo."'
                            
                            INNER JOIN ( SELECT 
                                            cod_contrato        
                                            ,tipo_calculo
                                            ,SUM(valor) as valor
                                            FROM (
                                                    SELECT * 
                                                            ,'E' as tipo_calculo
                                                    from recuperarEventosCalculados(0,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') WHERE natureza ='P'
                                                    UNION
                                                    SELECT * 
                                                            ,'M' as tipo_calculo
                                                    from recuperarEventosCalculados(1,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') WHERE natureza ='P'
                                                    UNION
                                                    SELECT * 
                                                            ,'M' as tipo_calculo
                                                    from recuperarEventosCalculados(2,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') WHERE natureza ='P'
                                                    UNION
                                                    SELECT * 
                                                            ,'D' as tipo_calculo
                                                    from recuperarEventosCalculados(3,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') WHERE natureza ='P'
                                                    UNION
                                                    SELECT * 
                                                            ,'M' as tipo_calculo
                                                    from recuperarEventosCalculados(4,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') WHERE natureza ='P'
                                                    AND desdobramento != 'D'
                                                    UNION
                                                    SELECT * 
                                                            ,'D' as tipo_calculo
                                                    from recuperarEventosCalculados(4,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') WHERE natureza ='P'
                                                    AND desdobramento = 'D'
                                                    ) as retorno
                                                    GROUP by 1,2
                                    ) as remuneracoes
                                            ON remuneracoes.cod_contrato = contrato_servidor_periodo.cod_contrato
                            LEFT JOIN (
                                            SELECT 
                                            cod_contrato      
                                            ,tipo_calculo          
                                            ,SUM(valor) as valor
                                                    FROM (
                                                    SELECT complementar.*
                                                            ,'E' as tipo_calculo
                                                    from recuperarEventosCalculados(0,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') as complementar
                                                    INNER JOIN pessoal.contrato_servidor
                                                        ON contrato_servidor.cod_contrato = complementar.cod_contrato
                                                    INNER JOIN ( SELECT 
                                                                            max.cod_evento
                                                                            ,MAX(max.timestamp) as timestamp
                                                                    FROM folhapagamento.tabela_irrf_evento as max
                                                                    where cod_tipo IN (3,6)
                                                                    GROUP BY max.cod_evento , max.cod_tabela                
                                                            UNION
                                                                    SELECT        
                                                                            previdencia_evento.cod_evento
                                                                            ,MAX(previdencia_evento.timestamp)as timestamp
                                                                    FROM folhapagamento.previdencia_evento
                                                                    INNER JOIN folhapagamento.previdencia_previdencia 
                                                                            ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                                            AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp                
                                                                    WHERE cod_tipo = 1
                                                                    AND tipo_previdencia = 'o'
                                                                    GROUP BY previdencia_evento.cod_evento
                                                                    ,previdencia_evento.cod_tipo
                                                            ) as irrf_previdencia_evento
                                                                    ON irrf_previdencia_evento.cod_evento = complementar.cod_evento
                                                    UNION
                                                    SELECT calculado.*         
                                                            ,'M' as tipo_calculo
                                                    from recuperarEventosCalculados(1,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') as calculado
                                                    INNER JOIN pessoal.contrato_servidor
                                                        ON contrato_servidor.cod_contrato = calculado.cod_contrato
                                                    INNER JOIN ( SELECT 
                                                                            max.cod_evento
                                                                            ,MAX(max.timestamp) as timestamp
                                                                    FROM folhapagamento.tabela_irrf_evento as max
                                                                    where cod_tipo IN (3,6)
                                                                    GROUP BY max.cod_evento , max.cod_tabela                
                                                            UNION
                                                                    SELECT        
                                                                            previdencia_evento.cod_evento
                                                                            ,MAX(previdencia_evento.timestamp)as timestamp
                                                                    FROM folhapagamento.previdencia_evento
                                                                    INNER JOIN folhapagamento.previdencia_previdencia 
                                                                            ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                                            AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp                
                                                                    WHERE cod_tipo = 1
                                                                    AND tipo_previdencia = 'o'
                                                                    GROUP BY previdencia_evento.cod_evento
                                                                    ,previdencia_evento.cod_tipo
                                                            ) as irrf_previdencia_evento
                                                                    ON irrf_previdencia_evento.cod_evento = calculado.cod_evento
                                                    UNION
                                                    SELECT ferias.*         
                                                            ,'M' as tipo_calculo
                                                    from recuperarEventosCalculados(2,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') as ferias
                                                    INNER JOIN pessoal.contrato_servidor
                                                        ON contrato_servidor.cod_contrato = ferias.cod_contrato
                                                    INNER JOIN ( SELECT 
                                                                            max.cod_evento
                                                                            ,MAX(max.timestamp) as timestamp
                                                                    FROM folhapagamento.tabela_irrf_evento as max
                                                                    where cod_tipo IN (3,6)
                                                                    GROUP BY max.cod_evento , max.cod_tabela                
                                                            UNION
                                                                    SELECT        
                                                                            previdencia_evento.cod_evento
                                                                            ,MAX(previdencia_evento.timestamp)as timestamp
                                                                    FROM folhapagamento.previdencia_evento
                                                                    INNER JOIN folhapagamento.previdencia_previdencia 
                                                                            ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                                            AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp                
                                                                    WHERE cod_tipo = 1
                                                                    AND tipo_previdencia = 'o'
                                                                    GROUP BY previdencia_evento.cod_evento
                                                                    ,previdencia_evento.cod_tipo
                                                            ) as irrf_previdencia_evento
                                                                    ON irrf_previdencia_evento.cod_evento = ferias.cod_evento
                                                    UNION
                                                    SELECT decimo.* 
                                                            ,'D' as tipo_calculo
                                                    from recuperarEventosCalculados(3,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') as decimo
                                                    INNER JOIN pessoal.contrato_servidor
                                                        ON contrato_servidor.cod_contrato = decimo.cod_contrato
                                                    INNER JOIN ( SELECT 
                                                                            max.cod_evento
                                                                            ,MAX(max.timestamp) as timestamp
                                                                    FROM folhapagamento.tabela_irrf_evento as max
                                                                    where cod_tipo IN (3,6)
                                                                    GROUP BY max.cod_evento , max.cod_tabela                
                                                            UNION
                                                                    SELECT        
                                                                            previdencia_evento.cod_evento
                                                                            ,MAX(previdencia_evento.timestamp)as timestamp
                                                                    FROM folhapagamento.previdencia_evento
                                                                    INNER JOIN folhapagamento.previdencia_previdencia 
                                                                            ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                                            AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp                
                                                                    WHERE cod_tipo = 1
                                                                    AND tipo_previdencia = 'o'
                                                                    GROUP BY previdencia_evento.cod_evento
                                                                    ,previdencia_evento.cod_tipo
                                                            ) as irrf_previdencia_evento
                                                                    ON irrf_previdencia_evento.cod_evento = decimo.cod_evento
                                                    UNION
                                                    SELECT rescisao.*        
                                                            ,'M' as tipo_calculo 
                                                    from recuperarEventosCalculados(4,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') as rescisao
                                                    INNER JOIN pessoal.contrato_servidor
                                                        ON contrato_servidor.cod_contrato = rescisao.cod_contrato
                                                    INNER JOIN ( SELECT 
                                                                            max.cod_evento
                                                                            ,MAX(max.timestamp) as timestamp
                                                                    FROM folhapagamento.tabela_irrf_evento as max
                                                                    where cod_tipo IN (3,6)
                                                                    GROUP BY max.cod_evento , max.cod_tabela                
                                                            UNION
                                                                    SELECT        
                                                                            previdencia_evento.cod_evento
                                                                            ,MAX(previdencia_evento.timestamp)as timestamp
                                                                    FROM folhapagamento.previdencia_evento
                                                                    INNER JOIN folhapagamento.previdencia_previdencia 
                                                                            ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                                            AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp                
                                                                    WHERE cod_tipo = 1
                                                                    AND tipo_previdencia = 'o'
                                                                    GROUP BY previdencia_evento.cod_evento
                                                                    ,previdencia_evento.cod_tipo
                                                            ) as irrf_previdencia_evento
                                                                    ON irrf_previdencia_evento.cod_evento = rescisao.cod_evento
                                                    WHERE rescisao.desdobramento != 'D'
                                                    UNION
                                                    SELECT rescisao.*        
                                                            ,'D' as tipo_calculo 
                                                    from recuperarEventosCalculados(4,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') as rescisao
                                                    INNER JOIN pessoal.contrato_servidor
                                                        ON contrato_servidor.cod_contrato = rescisao.cod_contrato
                                                    INNER JOIN ( SELECT 
                                                                            max.cod_evento
                                                                            ,MAX(max.timestamp) as timestamp
                                                                    FROM folhapagamento.tabela_irrf_evento as max
                                                                    where cod_tipo IN (3,6)
                                                                    GROUP BY max.cod_evento , max.cod_tabela                
                                                            UNION
                                                                    SELECT        
                                                                            previdencia_evento.cod_evento
                                                                            ,MAX(previdencia_evento.timestamp)as timestamp
                                                                    FROM folhapagamento.previdencia_evento
                                                                    INNER JOIN folhapagamento.previdencia_previdencia 
                                                                            ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                                            AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp                
                                                                    WHERE cod_tipo = 1
                                                                    AND tipo_previdencia = 'o'
                                                                    GROUP BY previdencia_evento.cod_evento
                                                                    ,previdencia_evento.cod_tipo
                                                            ) as irrf_previdencia_evento
                                                                    ON irrf_previdencia_evento.cod_evento = rescisao.cod_evento
                                                    WHERE rescisao.desdobramento = 'D'
                                                    ) as retorno
                                                    GROUP by 1,2
                                                    ORDER BY cod_contrato
                                    ) AS irrf_previdencia
                                            ON irrf_previdencia.cod_contrato = contrato_servidor_periodo.cod_contrato
                                            AND irrf_previdencia.tipo_calculo = remuneracoes.tipo_calculo

            UNION

            --PENSIONISTA

            SELECT 
                10 as tipo_registro
                ,sw_cgm_pessoa_fisica.cpf AS num_cpf
                ,CASE WHEN remuneracoes.tipo_calculo = 'M' THEN
                        contrato.registro||''||1
                      WHEN remuneracoes.tipo_calculo = 'D' THEN
                        contrato.registro||''||2
                      WHEN remuneracoes.tipo_calculo = 'E' THEN
                        contrato.registro||''||3
                END as cod_reduzido_pessoa
                ,'C' as regime
                ,remuneracoes.tipo_calculo as tipo_calculo
                ,'P' as situacao_servidor_pensionista
                ,'' as descricao_situacao
                ,TO_CHAR(contrato_pensionista.dt_inicio_beneficio,'ddmmyyyy') as data_concessao_aposentadoria_pensao
                ,remove_acentos(cargo.descricao) as nome_cargo
                ,SUBSTR(tcemg_entidade_cargo_pensionista.descricao,1,3) as sigla_cargo
                ,SUBSTR(tcemg_entidade_cargo_pensionista.descricao,7) AS descricao_sigla_cargo
                ,COALESCE(tcemg_entidade_requisitos_cargo.cod_tipo,4) as requisito_cargo
                , CASE WHEN adido_cedido.tipo_cedencia = 'c' AND indicativo_onus = 'c'
                        THEN 'SCO'
                        WHEN adido_cedido.tipo_cedencia = 'c' AND indicativo_onus = 'e'
                        THEN 'SCS'
                        ELSE ''
                END AS indicador_cessao
                ,orgao_descricao.descricao as nome_lotacao
                ,'00'::VARCHAR as valor_horas_semanais
                ,TO_CHAR(contrato_pensionista_funcao.vigencia,'ddmmyyyy') as data_efetivacao_exercicio_cargo
                ,TO_CHAR(contrato_pensionista.dt_encerramento,'ddmmyyyy') as data_exclusao                
                ,COALESCE(remuneracoes.valor,0.00) as valor_remuneracao_bruto
                ,CASE WHEN (remuneracoes.valor - COALESCE(irrf_previdencia.valor,0.00)) >= 0 THEN
                        'C'
                        ELSE
                        'D'
                END as natureza_saldo_liquido                
                ,((remuneracoes.valor - COALESCE(irrf_previdencia.valor,0.00))) as valor_remuneracao_liquida
                ,COALESCE(irrf_previdencia.valor,0.00) as valor_obrigacoes                
                
            FROM pessoal.contrato_pensionista

            INNER JOIN pessoal.pensionista
                ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente

            INNER JOIN pessoal.contrato
                ON contrato.cod_contrato = contrato_pensionista.cod_contrato

            INNER JOIN sw_cgm
                ON sw_cgm.numcgm = pensionista.numcgm

            INNER JOIN sw_cgm_pessoa_fisica
                ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                
            INNER JOIN ultimo_contrato_servidor_funcao('".$stEntidade."', '".$inCodPeriodoMovimentacao."') as ultimo_contrato_pensionista_funcao
                ON contrato_pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_funcao.cod_contrato

            INNER JOIN ( SELECT contrato_servidor_funcao.*
                         FROM pessoal.contrato_servidor_funcao
                         INNER JOIN ( SELECT cod_contrato
                                             ,cod_cargo
                                             ,MAX(timestamp) as timestamp
                                       from pessoal.contrato_servidor_funcao
                                       WHERE timestamp <= ultimoTimestampPeriodoMovimentacao(".$inCodPeriodoMovimentacao.",'".$stEntidade."')::TIMESTAMP
                                       group by 1,2
                            ) as max
                                ON max.cod_contrato     = contrato_servidor_funcao.cod_contrato
                                AND max.cod_cargo       = contrato_servidor_funcao.cod_cargo
                                AND max.timestamp       = contrato_servidor_funcao.timestamp
                ) as contrato_pensionista_funcao
                    ON contrato_pensionista_funcao.cod_contrato = ultimo_contrato_pensionista_funcao.cod_contrato
                    AND contrato_pensionista_funcao.cod_cargo = ultimo_contrato_pensionista_funcao.cod_cargo                
                                        
            INNER JOIN pessoal.cargo
                ON cargo.cod_cargo = contrato_pensionista_funcao.cod_cargo
                    
            INNER JOIN ultimo_contrato_servidor_sub_divisao_funcao('".$stEntidade."', '".$inCodPeriodoMovimentacao."') as ultimo_contrato_pensionista_sub_divisao_funcao
                ON contrato_pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_sub_divisao_funcao.cod_contrato
                    
            INNER JOIN ( SELECT cargo_sub_divisao.*
                         FROM pessoal.cargo_sub_divisao
                         INNER JOIN( SELECT cargo_sub_divisao.cod_cargo
                                            ,cargo_sub_divisao.cod_sub_divisao
                                         , max(cargo_sub_divisao.timestamp) as timestamp
                                         FROM pessoal.cargo_sub_divisao
                                         WHERE cargo_sub_divisao.timestamp <= ultimoTimestampPeriodoMovimentacao(".$inCodPeriodoMovimentacao.",'".$stEntidade."')::TIMESTAMP
                                         GROUP BY cod_cargo,cod_sub_divisao
                                 )as max_cargo_sub_divisao
                                 ON cargo_sub_divisao.cod_cargo = max_cargo_sub_divisao.cod_cargo
                                 AND cargo_sub_divisao.cod_sub_divisao = max_cargo_sub_divisao.cod_sub_divisao
                                 AND cargo_sub_divisao.timestamp = max_cargo_sub_divisao.timestamp
            ) as cargo_sub_divisao
                ON cargo_sub_divisao.cod_cargo = contrato_pensionista_funcao.cod_cargo
                AND cargo_sub_divisao.cod_sub_divisao = ultimo_contrato_pensionista_sub_divisao_funcao.cod_sub_divisao_funcao

            INNER JOIN ( SELECT 
                        cod_contrato        
                        ,tipo_calculo
                        ,SUM(valor) as valor
                        FROM (
                                SELECT * 
                                        ,'E' as tipo_calculo
                                from recuperarEventosCalculados(0,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') WHERE natureza ='P'
                                UNION
                                SELECT * 
                                        ,'M' as tipo_calculo
                                from recuperarEventosCalculados(1,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') WHERE natureza ='P'
                                UNION
                                SELECT * 
                                        ,'M' as tipo_calculo
                                from recuperarEventosCalculados(2,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') WHERE natureza ='P'
                                UNION
                                SELECT * 
                                        ,'D' as tipo_calculo
                                from recuperarEventosCalculados(3,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') WHERE natureza ='P'
                                UNION
                                SELECT * 
                                        ,'M' as tipo_calculo
                                from recuperarEventosCalculados(4,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') WHERE natureza ='P'
                                AND desdobramento != 'D'
                                UNION
                                SELECT * 
                                        ,'D' as tipo_calculo
                                from recuperarEventosCalculados(4,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') WHERE natureza ='P'
                                AND desdobramento = 'D'
                                
                                ) as retorno
                                GROUP by 1,2
                ) as remuneracoes
                ON remuneracoes.cod_contrato = contrato_pensionista.cod_contrato

            LEFT JOIN ( SELECT 
                            tipo_cargo_servidor.descricao
                            ,tcemg_entidade_cargo_servidor.cod_cargo
                            ,tcemg_entidade_cargo_servidor.cod_sub_divisao
                            
                            from folhapagamento.tcemg_entidade_cargo_servidor
                            INNER JOIN tcemg.tipo_cargo_servidor
                                    ON tipo_cargo_servidor.cod_tipo = tcemg_entidade_cargo_servidor.cod_tipo
                            INNER JOIN pessoal.sub_divisao
                                    ON sub_divisao.cod_sub_divisao = tcemg_entidade_cargo_servidor.cod_sub_divisao
                            INNER JOIN pessoal.cargo
                                    ON cargo.cod_cargo = tcemg_entidade_cargo_servidor.cod_cargo
                            WHERE tcemg_entidade_cargo_servidor.exercicio = '".Sessao::getExercicio()."'
                ) as tcemg_entidade_cargo_pensionista
                    ON tcemg_entidade_cargo_pensionista.cod_cargo = contrato_pensionista_funcao.cod_cargo
                    AND tcemg_entidade_cargo_pensionista.cod_sub_divisao = ultimo_contrato_pensionista_sub_divisao_funcao.cod_sub_divisao_funcao
                                    
            LEFT JOIN ( SELECT 
                            tipo_requisitos_cargo.descricao
                            ,tcemg_entidade_requisitos_cargo.cod_tipo
                            ,tcemg_entidade_requisitos_cargo.cod_cargo                
                            from folhapagamento.tcemg_entidade_requisitos_cargo
                            INNER JOIN tcemg.tipo_requisitos_cargo
                                    ON tipo_requisitos_cargo.cod_tipo = tcemg_entidade_requisitos_cargo.cod_tipo
                            INNER JOIN pessoal.cargo
                                    ON cargo.cod_cargo = tcemg_entidade_requisitos_cargo.cod_cargo
                            WHERE tcemg_entidade_requisitos_cargo.exercicio = '".Sessao::getExercicio()."'
                ) as tcemg_entidade_requisitos_cargo
                    ON tcemg_entidade_requisitos_cargo.cod_cargo = contrato_pensionista_funcao.cod_cargo

            LEFT JOIN ( SELECT adido_cedido.*
                        FROM pessoal.adido_cedido
                        LEFT JOIN pessoal.adido_cedido_excluido
                                ON adido_cedido_excluido.cod_contrato            = adido_cedido.cod_contrato
                                AND adido_cedido_excluido.cod_norma              = adido_cedido.cod_norma
                                AND adido_cedido_excluido.timestamp_cedido_adido = adido_cedido.timestamp
                        WHERE adido_cedido_excluido.cod_contrato IS NOT NULL
                        AND adido_cedido.timestamp between '".$stDataInicialPeriodo."' AND '".$stDataFinalPeriodo."'
                ) as adido_cedido
                    ON adido_cedido.cod_contrato = contrato_pensionista.cod_contrato

            INNER JOIN ultimo_contrato_pensionista_orgao('".$stEntidade."', '".$inCodPeriodoMovimentacao."') as ultimo_contrato_pensionista_orgao
                ON contrato_pensionista.cod_contrato = ultimo_contrato_pensionista_orgao.cod_contrato

            INNER JOIN (SELECT orgao_descricao.cod_orgao
                                ,orgao_descricao.descricao
                                ,orgao_descricao.timestamp
                                FROM organograma.orgao_descricao
                                INNER JOIN( SELECT cod_orgao
                                                   ,MAX(timestamp) as timestamp
                                                FROM organograma.orgao_descricao
                                                WHERE timestamp <= ultimoTimestampPeriodoMovimentacao(".$inCodPeriodoMovimentacao.",'".$stEntidade."')::TIMESTAMP
                                                group BY 1
                                )as max 
                                        ON max.cod_orgao = orgao_descricao.cod_orgao
                                        AND max.timestamp = orgao_descricao.timestamp                                                
                    ) as orgao_descricao
                ON orgao_descricao.cod_orgao = ultimo_contrato_pensionista_orgao.cod_orgao
                AND orgao_descricao.timestamp <= ultimoTimestampPeriodoMovimentacao(".$inCodPeriodoMovimentacao.",'".$stEntidade."')::TIMESTAMP

            LEFT JOIN (
                        SELECT 
                        cod_contrato      
                        ,tipo_calculo          
                        ,SUM(valor) as valor
                                FROM (
                                SELECT complementar.*
                                        ,'E' as tipo_calculo
                                from recuperarEventosCalculados(0,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') as complementar
                                INNER JOIN pessoal.contrato_pensionista
                                        ON contrato_pensionista.cod_contrato = complementar.cod_contrato
                                INNER JOIN ( SELECT 
                                                        max.cod_evento
                                                        ,MAX(max.timestamp) as timestamp
                                                FROM folhapagamento.tabela_irrf_evento as max
                                                where cod_tipo IN (3,6)
                                                GROUP BY max.cod_evento , max.cod_tabela                
                                        UNION
                                                SELECT        
                                                        previdencia_evento.cod_evento
                                                        ,MAX(previdencia_evento.timestamp)as timestamp
                                                FROM folhapagamento.previdencia_evento
                                                INNER JOIN folhapagamento.previdencia_previdencia 
                                                        ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                        AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp                
                                                WHERE cod_tipo = 1
                                                AND tipo_previdencia = 'o'
                                                GROUP BY previdencia_evento.cod_evento
                                                ,previdencia_evento.cod_tipo
                                        ) as irrf_previdencia_evento
                                                ON irrf_previdencia_evento.cod_evento = complementar.cod_evento
                                UNION
                                SELECT calculado.*         
                                        ,'M' as tipo_calculo
                                from recuperarEventosCalculados(1,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') as calculado
                                INNER JOIN pessoal.contrato_pensionista
                                        ON contrato_pensionista.cod_contrato = calculado.cod_contrato
                                INNER JOIN ( SELECT 
                                                        max.cod_evento
                                                        ,MAX(max.timestamp) as timestamp
                                                FROM folhapagamento.tabela_irrf_evento as max
                                                where cod_tipo IN (3,6)
                                                GROUP BY max.cod_evento , max.cod_tabela                
                                        UNION
                                                SELECT        
                                                        previdencia_evento.cod_evento
                                                        ,MAX(previdencia_evento.timestamp)as timestamp
                                                FROM folhapagamento.previdencia_evento
                                                INNER JOIN folhapagamento.previdencia_previdencia 
                                                        ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                        AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp                
                                                WHERE cod_tipo = 1
                                                AND tipo_previdencia = 'o'
                                                GROUP BY previdencia_evento.cod_evento
                                                ,previdencia_evento.cod_tipo
                                        ) as irrf_previdencia_evento
                                                ON irrf_previdencia_evento.cod_evento = calculado.cod_evento
                                UNION
                                SELECT ferias.*         
                                        ,'M' as tipo_calculo
                                from recuperarEventosCalculados(2,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') as ferias
                                INNER JOIN pessoal.contrato_pensionista
                                        ON contrato_pensionista.cod_contrato = ferias.cod_contrato
                                INNER JOIN ( SELECT 
                                                        max.cod_evento
                                                        ,MAX(max.timestamp) as timestamp
                                                FROM folhapagamento.tabela_irrf_evento as max
                                                where cod_tipo IN (3,6)
                                                GROUP BY max.cod_evento , max.cod_tabela                
                                        UNION
                                                SELECT        
                                                        previdencia_evento.cod_evento
                                                        ,MAX(previdencia_evento.timestamp)as timestamp
                                                FROM folhapagamento.previdencia_evento
                                                INNER JOIN folhapagamento.previdencia_previdencia 
                                                        ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                        AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp                
                                                WHERE cod_tipo = 1
                                                AND tipo_previdencia = 'o'
                                                GROUP BY previdencia_evento.cod_evento
                                                ,previdencia_evento.cod_tipo
                                        ) as irrf_previdencia_evento
                                                ON irrf_previdencia_evento.cod_evento = ferias.cod_evento
                                UNION
                                SELECT decimo.* 
                                        ,'D' as tipo_calculo
                                from recuperarEventosCalculados(3,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') as decimo
                                INNER JOIN pessoal.contrato_pensionista
                                        ON contrato_pensionista.cod_contrato = decimo.cod_contrato
                                INNER JOIN ( SELECT 
                                                        max.cod_evento
                                                        ,MAX(max.timestamp) as timestamp
                                                FROM folhapagamento.tabela_irrf_evento as max
                                                where cod_tipo IN (3,6)
                                                GROUP BY max.cod_evento , max.cod_tabela                
                                        UNION
                                                SELECT        
                                                        previdencia_evento.cod_evento
                                                        ,MAX(previdencia_evento.timestamp)as timestamp
                                                FROM folhapagamento.previdencia_evento
                                                INNER JOIN folhapagamento.previdencia_previdencia 
                                                        ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                        AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp                
                                                WHERE cod_tipo = 1
                                                AND tipo_previdencia = 'o'
                                                GROUP BY previdencia_evento.cod_evento
                                                ,previdencia_evento.cod_tipo
                                        ) as irrf_previdencia_evento
                                                ON irrf_previdencia_evento.cod_evento = decimo.cod_evento
                                UNION
                                SELECT rescisao.*        
                                        ,'M' as tipo_calculo 
                                from recuperarEventosCalculados(4,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') as rescisao
                                INNER JOIN pessoal.contrato_servidor
                                    ON contrato_servidor.cod_contrato = rescisao.cod_contrato
                                INNER JOIN ( SELECT 
                                                        max.cod_evento
                                                        ,MAX(max.timestamp) as timestamp
                                                FROM folhapagamento.tabela_irrf_evento as max
                                                where cod_tipo IN (3,6)
                                                GROUP BY max.cod_evento , max.cod_tabela                
                                        UNION
                                                SELECT        
                                                        previdencia_evento.cod_evento
                                                        ,MAX(previdencia_evento.timestamp)as timestamp
                                                FROM folhapagamento.previdencia_evento
                                                INNER JOIN folhapagamento.previdencia_previdencia 
                                                        ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                        AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp                
                                                WHERE cod_tipo = 1
                                                AND tipo_previdencia = 'o'
                                                GROUP BY previdencia_evento.cod_evento
                                                ,previdencia_evento.cod_tipo
                                        ) as irrf_previdencia_evento
                                                ON irrf_previdencia_evento.cod_evento = rescisao.cod_evento
                                WHERE rescisao.desdobramento != 'D'
                                UNION
                                SELECT rescisao.*        
                                        ,'D' as tipo_calculo 
                                from recuperarEventosCalculados(4,'".$inCodPeriodoMovimentacao."',0,0,'".$stEntidade."','') as rescisao
                                INNER JOIN pessoal.contrato_servidor
                                    ON contrato_servidor.cod_contrato = rescisao.cod_contrato
                                INNER JOIN ( SELECT 
                                                        max.cod_evento
                                                        ,MAX(max.timestamp) as timestamp
                                                FROM folhapagamento.tabela_irrf_evento as max
                                                where cod_tipo IN (3,6)
                                                GROUP BY max.cod_evento , max.cod_tabela                
                                        UNION
                                                SELECT        
                                                        previdencia_evento.cod_evento
                                                        ,MAX(previdencia_evento.timestamp)as timestamp
                                                FROM folhapagamento.previdencia_evento
                                                INNER JOIN folhapagamento.previdencia_previdencia 
                                                        ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                        AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp                
                                                WHERE cod_tipo = 1
                                                AND tipo_previdencia = 'o'
                                                GROUP BY previdencia_evento.cod_evento
                                                ,previdencia_evento.cod_tipo
                                        ) as irrf_previdencia_evento
                                                ON irrf_previdencia_evento.cod_evento = rescisao.cod_evento
                                WHERE rescisao.desdobramento = 'D'
                                
                                ) as retorno
                                GROUP by 1,2
                                ORDER BY cod_contrato
            ) AS irrf_previdencia
                ON irrf_previdencia.cod_contrato = contrato_pensionista.cod_contrato
                AND irrf_previdencia.tipo_calculo = remuneracoes.tipo_calculo                

            ) as resultado

            ";
        
        return $stSql;
    }
    
    public function recuperaDadosExportacaoFolhaPagamento11(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaDadosExportacaoFolhaPagamento11();
        $this->stDebug = $stSql;                
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    } 
    
    public function montaRecuperaDadosExportacaoFolhaPagamento11()
    {
        $inCodPeriodoMovimentacao = $this->getDado('cod_periodo_movimentacao');
        $stDataInicialPeriodo = $this->getDado('data_inicial_periodo');        
        $stDataFinalPeriodo = $this->getDado('data_final_periodo');
        $stExercicio = $this->getDado('exercicio');
        $stEntidade = Sessao::getEntidade();
        $inCodEntidade = $this->getDado('cod_entidade');

        $stSql = " SELECT * FROM (
                    SELECT tipo_registro
                           ,cod_reduzido_pessoa
                           ,num_cpf
                           ,LPAD(tipo_remuneracao::varchar,2,'0') as tipo_remuneracao
                           ,descricao_outros                           
                           ,SUM(valor_remuneracao) as valor_remuneracao 
                    FROM(
                    SELECT DISTINCT
                        11 as tipo_registro
                        ,CASE WHEN remuneracoes.tipo_calculo = 'M' THEN
                                        contrato.registro||''||1
                                WHEN remuneracoes.tipo_calculo = 'D' THEN
                                        contrato.registro||''||2
                                WHEN remuneracoes.tipo_calculo = 'E' THEN
                                        contrato.registro||''||3
                        END as cod_reduzido_pessoa                
                        ,sw_cgm_pessoa_fisica.cpf AS num_cpf
                        ,tcemg_entidade_remuneracao.cod_tipo as tipo_remuneracao
                        ,CASE WHEN tcemg_entidade_remuneracao.cod_tipo = 99 OR tcemg_entidade_remuneracao.cod_tipo = 09
                                THEN remuneracoes.descricao
                                ELSE ''
                        END AS descricao_outros                        
                        ,COALESCE(remuneracoes.valor,0.00) as valor_remuneracao
                        
                        FROM pessoal.contrato
                    
                            INNER JOIN folhapagamento.contrato_servidor_periodo
                                    ON contrato_servidor_periodo.cod_contrato = contrato.cod_contrato
                                    AND contrato_servidor_periodo.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao."
                                    
                            INNER JOIN pessoal.contrato_servidor
                                    ON contrato_servidor.cod_contrato = contrato.cod_contrato
                    
                            INNER JOIN pessoal.servidor_contrato_servidor
                                    ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato
                    
                            INNER JOIN pessoal.servidor
                                    ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
                    
                            INNER JOIN sw_cgm_pessoa_fisica
                                    ON sw_cgm_pessoa_fisica.numcgm = servidor.numcgm
                      
                        INNER JOIN ( SELECT 
                                            cod_contrato        
                                            ,tipo_calculo
                                            ,cod_evento
                                            ,descricao
                                            ,SUM(valor) as valor
                                            FROM (
                                                    SELECT * 
                                                            ,'E' as tipo_calculo
                                                    from recuperarEventosCalculados(0,'".$inCodPeriodoMovimentacao."',0,0,'','') WHERE natureza ='P'
                                                    UNION
                                                    SELECT * 
                                                            ,'M' as tipo_calculo
                                                    from recuperarEventosCalculados(1,'".$inCodPeriodoMovimentacao."',0,0,'','') WHERE natureza ='P'
                                                    UNION
                                                    SELECT * 
                                                            ,'M' as tipo_calculo
                                                    from recuperarEventosCalculados(2,'".$inCodPeriodoMovimentacao."',0,0,'','') WHERE natureza ='P'
                                                    UNION
                                                    SELECT * 
                                                            ,'D' as tipo_calculo
                                                    from recuperarEventosCalculados(3,'".$inCodPeriodoMovimentacao."',0,0,'','') WHERE natureza ='P'
                                                    UNION
                                                    SELECT * 
                                                            ,'M' as tipo_calculo
                                                    from recuperarEventosCalculados(4,'".$inCodPeriodoMovimentacao."',0,0,'','') WHERE natureza ='P'
                                                    AND desdobramento != 'D'
                                                    UNION
                                                    SELECT * 
                                                            ,'D' as tipo_calculo
                                                    from recuperarEventosCalculados(4,'".$inCodPeriodoMovimentacao."',0,0,'','') WHERE natureza ='P'
                                                    AND desdobramento = 'D'
                                                    ) as retorno
                                                    GROUP by 1,2,3,4
                                    ) as remuneracoes
                                            ON remuneracoes.cod_contrato = contrato_servidor_periodo.cod_contrato

                       LEFT JOIN folhapagamento.tcemg_entidade_remuneracao
                                ON tcemg_entidade_remuneracao.cod_evento = remuneracoes.cod_evento
                                AND tcemg_entidade_remuneracao.exercicio = '".Sessao::getExercicio()."'

        ) as servidor
        GROUP BY 1,2,3,4,5

    UNION ALL
        
        SELECT tipo_registro
               ,cod_reduzido_pessoa
               ,num_cpf
               ,LPAD(tipo_remuneracao::varchar,2,'0') as tipo_remuneracao
               ,descricao_outros               
               ,SUM(valor_remuneracao) as valor_remuneracao 
        FROM(
        SELECT DISTINCT
                11 as tipo_registro
                ,CASE WHEN remuneracoes.tipo_calculo = 'M' THEN
                                contrato.registro||''||1
                        WHEN remuneracoes.tipo_calculo = 'D' THEN
                                contrato.registro||''||2
                        WHEN remuneracoes.tipo_calculo = 'E' THEN
                                contrato.registro||''||3
                END as cod_reduzido_pessoa        
                ,sw_cgm_pessoa_fisica.cpf AS num_cpf
                ,tcemg_entidade_remuneracao.cod_tipo as tipo_remuneracao
                ,CASE WHEN tcemg_entidade_remuneracao.cod_tipo = 99 OR tcemg_entidade_remuneracao.cod_tipo = 09
                        THEN remuneracoes.descricao
                        ELSE ''
                END AS descricao_outros
                ,COALESCE(remuneracoes.valor,0.00) as valor_remuneracao
        
        FROM pessoal.contrato_pensionista

        INNER JOIN pessoal.pensionista
            ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
            AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente

        INNER JOIN pessoal.contrato
            ON contrato.cod_contrato = contrato_pensionista.cod_contrato

        INNER JOIN sw_cgm
            ON sw_cgm.numcgm = pensionista.numcgm

        INNER JOIN sw_cgm_pessoa_fisica
            ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
        
                        INNER JOIN ( SELECT 
                                            cod_contrato        
                                            ,tipo_calculo
                                            ,cod_evento
                                            ,descricao
                                            ,SUM(valor) as valor
                                            FROM (                                                    
                                                        SELECT * 
                                                                ,'E' as tipo_calculo
                                                        from recuperarEventosCalculados(0,'".$inCodPeriodoMovimentacao."',0,0,'','') WHERE natureza ='P' 
                                                        UNION
                                                        SELECT * 
                                                                ,'M' as tipo_calculo
                                                        from recuperarEventosCalculados(1,'".$inCodPeriodoMovimentacao."',0,0,'','') WHERE natureza ='P'
                                                        UNION
                                                        SELECT * 
                                                                ,'M' as tipo_calculo
                                                        from recuperarEventosCalculados(2,'".$inCodPeriodoMovimentacao."',0,0,'','') WHERE natureza ='P'
                                                        UNION
                                                        SELECT * 
                                                                ,'D' as tipo_calculo
                                                        from recuperarEventosCalculados(3,'".$inCodPeriodoMovimentacao."',0,0,'','') WHERE natureza ='P'
                                                        UNION
                                                        SELECT * 
                                                                ,'M' as tipo_calculo
                                                        from recuperarEventosCalculados(4,'".$inCodPeriodoMovimentacao."',0,0,'','') WHERE natureza ='P'
                                                        AND desdobramento != 'D'
                                                        UNION
                                                        SELECT * 
                                                                ,'D' as tipo_calculo
                                                        from recuperarEventosCalculados(4,'".$inCodPeriodoMovimentacao."',0,0,'','') WHERE natureza ='P'
                                                        AND desdobramento = 'D'

                                                    ) as retorno                                                    
                                                    GROUP by 1,2,3,4
                                    ) as remuneracoes
                                            ON remuneracoes.cod_contrato = contrato_pensionista.cod_contrato

                        LEFT JOIN folhapagamento.tcemg_entidade_remuneracao
                                ON tcemg_entidade_remuneracao.cod_evento = remuneracoes.cod_evento
                                AND tcemg_entidade_remuneracao.exercicio = '".Sessao::getExercicio()."'

            ) as pensionistas
            GROUP BY 1,2,3,4,5
        )as resultado

        ORDER BY tipo_remuneracao::INTEGER

        ";
        
        return $stSql;
    }



    public function __destruct(){}

}//END OF CLASS

?>