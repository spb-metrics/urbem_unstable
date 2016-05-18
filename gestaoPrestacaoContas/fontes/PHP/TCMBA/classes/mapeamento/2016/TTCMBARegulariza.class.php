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
    * Extensão da Classe de mapeamento
    * Data de Criação: 13/06/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63040 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.03.00
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoUnidade.class.php" );

/**
  *
  * Data de Criação: 13/06/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTCMBARegulariza extends TOrcamentoUnidade
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::TOrcamentoUnidade();

    $this->setDado('exercicio', Sessao::getExercicio() );
}

function recuperaRegistro(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaRegistro().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRegistro()
{
  $stSql = "
            SELECT
                    1 AS tipo_registro
                  , tcmba_clc.exercicio_conciliacao AS exercicio
                  , '".$this->getDado('unidade_gestora')."' AS unidade_gestora
                  , tcmba_clc.cod_tipo_conciliacao AS tipo_conciliacao
                  , '' AS reservado_tcm
                  , CASE WHEN tcmba_clc.mes IS NOT NULL
                            THEN TO_CHAR(last_day(TO_DATE('01/'||tcmba_clc.mes::VARCHAR||'/'||tcmba_clc.exercicio::VARCHAR,'dd/mm/yyyy')),'dd/mm/yyyy')
                            ELSE ''
                    END AS data_regularizacao
                  , CASE WHEN conciliacao_lancamento_contabil.mes IS NOT NULL
                            THEN TO_CHAR(last_day(TO_DATE('01/'||conciliacao_lancamento_contabil.mes::VARCHAR||'/'||conciliacao_lancamento_contabil.exercicio::VARCHAR,'dd/mm/yyyy')),'dd/mm/yyyy')
                            ELSE ''
                    END AS data_conciliacao
                  , tcmba_clc.cod_plano AS conta_contabil
                  , tcmba_clc.exercicio_conciliacao||tcmba_clc.mes::VARCHAR AS competencia_regularizacao
                  , conciliacao_lancamento_contabil.exercicio_conciliacao||conciliacao_lancamento_contabil.mes::VARCHAR AS competencia_conciliacao
                  , tipo_conciliacao_lancamento_contabil.descricao AS descricao_regularizacao
                  , tcmba_clc.sequencia AS num_conciliacao
                  , valor_lancamento.vl_lancamento AS valor

              FROM tcmba.conciliacao_lancamento_contabil AS tcmba_clc

         LEFT JOIN tesouraria.conciliacao_lancamento_contabil
                ON conciliacao_lancamento_contabil.cod_plano             = tcmba_clc.cod_plano
               AND conciliacao_lancamento_contabil.exercicio_conciliacao = tcmba_clc.exercicio_conciliacao
               AND conciliacao_lancamento_contabil.exercicio             = tcmba_clc.exercicio
               AND conciliacao_lancamento_contabil.mes                   = tcmba_clc.mes              
               AND conciliacao_lancamento_contabil.cod_lote              = tcmba_clc.cod_lote
               AND conciliacao_lancamento_contabil.tipo                  = tcmba_clc.tipo
               AND conciliacao_lancamento_contabil.sequencia             = tcmba_clc.sequencia
               AND conciliacao_lancamento_contabil.cod_entidade          = tcmba_clc.cod_entidade
               AND conciliacao_lancamento_contabil.tipo_valor            = tcmba_clc.tipo_valor

        INNER JOIN tcmba.tipo_conciliacao_lancamento_contabil
                ON tipo_conciliacao_lancamento_contabil.cod_tipo_conciliacao = tcmba_clc.cod_tipo_conciliacao

        INNER JOIN contabilidade.valor_lancamento
                ON valor_lancamento.exercicio    = tcmba_clc.exercicio
               AND valor_lancamento.cod_entidade = tcmba_clc.cod_entidade
               AND valor_lancamento.tipo         = tcmba_clc.tipo
               AND valor_lancamento.cod_lote     = tcmba_clc.cod_lote
               AND valor_lancamento.sequencia    = tcmba_clc.sequencia
               AND valor_lancamento.tipo_valor   = tcmba_clc.tipo_valor

             WHERE tcmba_clc.exercicio = '".$this->getDado('exercicio')."'
               AND tcmba_clc.cod_entidade IN (".$this->getDado('entidades').")
               AND tcmba_clc.mes BETWEEN SPLIT_PART('".$this->getDado('data_inicial')."','/',2)::INTEGER
                                     AND SPLIT_PART('".$this->getDado('data_final')."','/',2)::INTEGER
          ";
    
    return $stSql;
}

}
