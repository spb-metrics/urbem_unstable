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

include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );

class TTCEMGAnulacaoEmpenho extends TEmpenhoEmpenho
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGAnulacaoEmpenho()
    {
        parent::TEmpenhoEmpenho();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaExportacao10(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera ("montaRecuperaExportacao10",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao10()
    {
        $stSql = "
                    SELECT
                            10 AS tipo_registro
                          , orgao.cod_orgao AS cod_orgao
                          , LPAD((LPAD(''||despesa.num_orgao,2, '0')||LPAD(''||despesa.num_unidade,2, '0')), 5, '0') AS cod_unidade 
                          , empenho.cod_empenho AS num_empenho
                          , TO_CHAR (empenho.dt_empenho, 'ddmmyyyy') AS dt_empenho
                          , TO_CHAR(empenho_anulado.timestamp,'ddmmyyyy') AS dt_anulacao
                          , tc.numero_anulacao_empenho( empenho.exercicio , empenho.cod_entidade,  empenho.cod_empenho, empenho_anulado.timestamp ) AS num_anulacao
                          , 1 AS tipo_anulacao -- fazer a análise de quais campos ou ações criar
                          , empenho_anulado.motivo AS espc_anl_emp
                          , ( SELECT SUM(vl_anulado) FROM empenho.empenho_anulado_item
                                                    WHERE empenho_anulado_item.exercicio = empenho_anulado.exercicio
                                                      AND empenho_anulado_item.cod_entidade = empenho_anulado.cod_entidade
                                                      AND empenho_anulado_item.cod_empenho = empenho_anulado.cod_empenho
                                                      AND empenho_anulado_item.timestamp = empenho_anulado.timestamp
                            ) AS vl_anulado

                    FROM empenho.empenho

                    JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

                    JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND pre_empenho_despesa.exercicio = pre_empenho.exercicio

                    JOIN orcamento.despesa
                      ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                     AND despesa.exercicio = pre_empenho_despesa.exercicio

                    JOIN empenho.empenho_anulado
                      ON empenho_anulado.exercicio = empenho.exercicio
                     AND empenho_anulado.cod_entidade = empenho.cod_entidade
                     AND empenho_anulado.cod_empenho = empenho.cod_empenho

                    JOIN (SELECT configuracao_entidade.cod_entidade AS cod_entidade
                                , orgao.num_orgao AS cod_orgao
                                , orgao.exercicio AS exercicio
                             FROM tcemg.orgao
                             JOIN administracao.configuracao_entidade
                               ON configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                              AND configuracao_entidade.cod_modulo = 55
                              AND configuracao_entidade.valor::integer = orgao.num_orgao
                              AND configuracao_entidade.exercicio = orgao.exercicio
                        ) AS orgao
                      ON orgao.exercicio = empenho.exercicio
                     AND orgao.cod_entidade = empenho.cod_entidade

                   WHERE empenho.cod_entidade IN (".$this->getDado('entidades').")
                     AND empenho.exercicio = '".$this->getDado('exercicio')."'
                     AND empenho_anulado.timestamp::date BETWEEN TO_DATE('01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy') AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || ".$this->getDado('mes')." || '-' || '01','yyyy-mm-dd'))

                GROUP BY cod_orgao, num_unidade, num_empenho, num_anulacao, dt_empenho, dt_anulacao, espc_anl_emp, empenho_anulado.exercicio,
             empenho_anulado.cod_entidade, empenho_anulado.cod_empenho, empenho_anulado.timestamp, despesa.num_orgao, despesa.num_unidade

                ORDER BY tipo_registro, cod_orgao, cod_unidade, num_empenho, num_anulacao
        ";
        return $stSql;
    }

    public function recuperaExportacao11(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera ("montaRecuperaExportacao11",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao11()
    {
        $stSql = "
                    SELECT
                            11 AS tipo_registro
                          , LPAD((LPAD(''||despesa.num_orgao,2, '0')||LPAD(''||despesa.num_unidade,2, '0')), 5, '0') AS cod_unidade 
                          , empenho.cod_empenho AS num_empenho
                          , tc.numero_anulacao_empenho( empenho.exercicio , empenho.cod_entidade,  empenho.cod_empenho, empenho_anulado.timestamp ) AS num_anulacao
                          , recurso_direto.codigo_tc AS cod_fonte_recurso
                          , SUM(empenho_anulado_item.vl_anulado) as vl_anulacao_fonte

                    FROM empenho.empenho

                    JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

                    JOIN empenho.item_pre_empenho
                      ON item_pre_empenho.exercicio = pre_empenho.exercicio
                    AND item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                    JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND pre_empenho_despesa.exercicio = pre_empenho.exercicio

                    JOIN orcamento.despesa
                      ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                     AND despesa.exercicio = pre_empenho_despesa.exercicio

                    JOIN orcamento.conta_despesa
                      ON pre_empenho_despesa.cod_conta = conta_despesa.cod_conta
                     AND pre_empenho_despesa.exercicio = conta_despesa.exercicio

                    JOIN empenho.empenho_anulado
                      ON empenho_anulado.exercicio = empenho.exercicio
                     AND empenho_anulado.cod_entidade = empenho.cod_entidade
                     AND empenho_anulado.cod_empenho = empenho.cod_empenho

                    JOIN empenho.empenho_anulado_item
                      ON empenho_anulado_item.exercicio = empenho_anulado.exercicio
                     AND empenho_anulado_item.cod_entidade = empenho_anulado.cod_entidade
                     AND empenho_anulado_item.cod_empenho = empenho_anulado.cod_empenho
                     AND empenho_anulado_item.timestamp = empenho_anulado.timestamp

                    JOIN (SELECT configuracao_entidade.cod_entidade AS cod_entidade
                                , orgao.num_orgao AS cod_orgao
                                , orgao.exercicio AS exercicio
                             FROM tcemg.orgao
                             JOIN administracao.configuracao_entidade
                               ON configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                              AND configuracao_entidade.cod_modulo = 55
                              AND configuracao_entidade.valor::integer = orgao.num_orgao
                              AND configuracao_entidade.exercicio = orgao.exercicio
                        ) AS orgao
                      ON orgao.exercicio = empenho.exercicio
                     AND orgao.cod_entidade = empenho.cod_entidade

                    JOIN orcamento.recurso
                      ON despesa.exercicio = recurso.exercicio
                     AND despesa.cod_recurso = recurso.cod_recurso

                    JOIN orcamento.recurso_direto
                      ON recurso_direto.exercicio = recurso.exercicio
                     AND recurso_direto.cod_recurso = recurso.cod_recurso

                   WHERE empenho.cod_entidade IN (".$this->getDado('entidades').")
                     AND empenho.exercicio = '".$this->getDado('exercicio')."'
                     AND empenho_anulado.timestamp::date BETWEEN TO_DATE('01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy') AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || ".$this->getDado('mes')." || '-' || '01','yyyy-mm-dd'))

                GROUP BY despesa.num_unidade, num_empenho, num_anulacao, recurso_direto.codigo_tc, despesa.num_orgao, despesa.num_unidade

                ORDER BY tipo_registro, cod_unidade, num_empenho, num_anulacao, cod_fonte_recurso
        ";
        return $stSql;
    }
    
    public function __destruct(){}

}
?>
