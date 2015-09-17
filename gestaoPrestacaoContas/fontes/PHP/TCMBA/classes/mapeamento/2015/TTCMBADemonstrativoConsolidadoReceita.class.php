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
    * Classe de mapeamento da tabela fn_demonstrativo_consolidado_receita
    * Data de Criação: 24/09/2004

    * @author Analista: Valtair
    * @author Desenvolvedor: Lisiane Morais
    * $id:$

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMBADemonstrativoConsolidadoReceita extends Persistente 
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    
    parent::Persistente();
    $this->setTabela('tcmba.fn_demonstrativo_consolidado_receita');

    $this->AddCampo('tipo_registro'             , 'varchar', false, ''    , false, false);
    $this->AddCampo('unidade_gestora'           , 'varchar', false, ''    , false, false);
    $this->AddCampo('competencia'               , 'varchar', false, ''    , false, false);
  
}

function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosTribunal()
{
    $stSql  = "
        SELECT 1  AS tipo_registro
             , '".$this->getDado('unidade_gestora')."' AS unidade_gestora
             , '".$this->getDado('exercicio').$this->getDado('mes')."' AS competencia
             , t.item_receita
             , SUM(t.valor_previsto) AS valor_previsto
             , SUM(t.arrecadado_mes)AS arrecadado_mes
             , SUM(t.arrecadado_ate_periodo) AS arrecadado_ate_periodo
             , SUM(t.anulado_mes) AS anulado_mes
             , SUM(t.anulado_ate_periodo) AS anulado_ate_periodo
             , 0.00 AS vl_diferenca_mais
             , 0.00 AS vl_diferenca_menos
         FROM( SELECT SUBSTR(REPLACE(tbl.cod_estrutural,'.',''),1,8) AS item_receita
                    , CASE WHEN SUBSTR(REPLACE(tbl.cod_estrutural,'.',''),1,1) = '9' THEN
                               CASE WHEN (tbl.valor_previsto > 0) THEN tbl.valor_previsto * -1
                                    ELSE tbl.valor_previsto
                               END
                           WHEN SUBSTR(REPLACE(tbl.cod_estrutural,'.',''),1,1) != '9' THEN
                               CASE WHEN (tbl.valor_previsto < 0) THEN tbl.valor_previsto * -1
                                    ELSE tbl.valor_previsto
                               END
                       END AS valor_previsto
                    , CASE WHEN SUBSTR(REPLACE(tbl.cod_estrutural,'.',''),1,1) = '9' THEN
                               CASE WHEN (tbl.arrecadado_mes > 0) THEN tbl.arrecadado_mes * -1
                                    ELSE tbl.arrecadado_mes
                               END
                           WHEN SUBSTR(REPLACE(tbl.cod_estrutural,'.',''),1,1) != '9' THEN
                               CASE WHEN (tbl.arrecadado_mes < 0) THEN tbl.arrecadado_mes * -1
                                   ELSE tbl.arrecadado_mes
                               END
                      END AS arrecadado_mes
                    , CASE WHEN SUBSTR(REPLACE(tbl.cod_estrutural,'.',''),1,1) = '9' THEN
                               CASE WHEN (tbl.arrecadado_ate_periodo > 0) THEN tbl.arrecadado_ate_periodo * -1
                                    ELSE tbl.arrecadado_ate_periodo
                               END
                           WHEN SUBSTR(REPLACE(tbl.cod_estrutural,'.',''),1,1) != '9' THEN
                               CASE WHEN (tbl.arrecadado_ate_periodo < 0) THEN tbl.arrecadado_ate_periodo * -1
                                    ELSE tbl.arrecadado_ate_periodo
                               END
                       END AS arrecadado_ate_periodo
                     , CASE WHEN SUBSTR(REPLACE(tbl.cod_estrutural,'.',''),1,1) != '9' THEN tbl.anulado_mes * -1
                            ELSE tbl.anulado_mes
                       END AS anulado_mes
                    , CASE WHEN SUBSTR(REPLACE(tbl.cod_estrutural,'.',''),1,1) != '9' THEN tbl.anulado_ate_periodo * -1
		                   ELSE tbl.anulado_ate_periodo
                       END AS anulado_ate_periodo
                   
                 FROM ".$this->getTabela()." ( '".$this->getDado("exercicio")."'
                                             ,'".$this->getDado("data_inicio")."'
                                             ,'".$this->getDado("data_fim")."'
                                             ,'".$this->getDado("entidades")." ') AS tbl
             ORDER BY item_receita ) AS t
             
     GROUP BY t.item_receita
             ";
    return $stSql;
}

}
?>