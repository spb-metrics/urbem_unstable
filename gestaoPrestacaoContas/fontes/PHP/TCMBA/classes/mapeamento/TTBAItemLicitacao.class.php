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
    * Data de Criação: 02/08/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

/*
$Log$
Revision 1.2  2007/10/02 18:17:17  hboaventura
inclusão do caso de uso uc-06.05.00

Revision 1.1  2007/08/09 01:05:49  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 02/08/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBAItemLicitacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTBAItemLicitacao()
{
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
    $this->setDado('exercicio', Sessao::getExercicio() );
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
    $stSql .= " SELECT   lici.exercicio     \n";
    $stSql .= "         ,lici.cod_licitacao     \n";
    $stSql .= "         ,mait.cod_item     \n";
    $stSql .= "         ,cait.descricao     \n";
    $stSql .= "         ,to_char(lici.timestamp,'dd/mm/yyyy') as data_homologacao     \n";
    $stSql .= "         ,to_char(lici.timestamp,'yyyymm') as competencia     \n";
    $stSql .= "         ,sum(mait.quantidade) as qtd_licitacao     \n";
    $stSql .= "         ,coalesce(sum(maia.qtd_anulacao),0.00) as qtd_anulacao     \n";
    $stSql .= "         ,sum(mait.quantidade) - coalesce(sum(maia.qtd_anulacao),0.00) as qtd_saldo     \n";
    $stSql .= "         ,unme.simbolo     \n";
    $stSql .= " FROM     licitacao.licitacao            as lici     \n";
    $stSql .= "         ,compras.mapa                   as mapa     \n";
    $stSql .= "         ,compras.mapa_item              as mait     \n";
    $stSql .= "         LEFT JOIN (     \n";
    $stSql .= "             SELECT   maia.exercicio     \n";
    $stSql .= "                     ,maia.cod_mapa     \n";
    $stSql .= "                     ,maia.exercicio_solicitacao     \n";
    $stSql .= "                     ,maia.cod_entidade     \n";
    $stSql .= "                     ,maia.cod_solicitacao     \n";
    $stSql .= "                     ,maia.cod_centro     \n";
    $stSql .= "                     ,maia.cod_item     \n";
    $stSql .= "                     ,maia.lote     \n";
    $stSql .= "                     ,sum(quantidade) as qtd_anulacao     \n";
    $stSql .= "             FROM    compras.mapa_item_anulacao as maia     \n";
    $stSql .= "             WHERE   maia.exercicio='".$this->getDado('exercicio')."'                      \n";
    if (trim($this->getDado('stEntidades'))) {
        $stSql .= " AND     maia.cod_entidade IN (".$this->getDado('stEntidades').")              \n";
    }
    $stSql .= "             GROUP BY maia.exercicio, maia.cod_mapa, maia.exercicio_solicitacao, maia.cod_entidade, maia.cod_solicitacao, maia.cod_centro, maia.cod_item, maia.lote     \n";
    $stSql .= "          ) as maia     \n";
    $stSql .= "         ON (     \n";
    $stSql .= "                     maia.exercicio              = mait.exercicio     \n";
    $stSql .= "             AND     maia.cod_mapa               = mait.cod_mapa     \n";
    $stSql .= "             AND     maia.exercicio_solicitacao  = mait.exercicio_solicitacao     \n";
    $stSql .= "             AND     maia.cod_entidade           = mait.cod_entidade     \n";
    $stSql .= "             AND     maia.cod_solicitacao        = mait.cod_solicitacao     \n";
    $stSql .= "             AND     maia.cod_centro             = mait.cod_centro     \n";
    $stSql .= "             AND     maia.cod_item               = mait.cod_item     \n";
    $stSql .= "             AND     maia.lote                   = mait.lote     \n";
    $stSql .= "         )     \n";
    $stSql .= "         ,almoxarifado.catalogo_item     as cait     \n";
    $stSql .= "         ,administracao.unidade_medida   as unme     \n";
    $stSql .= " WHERE   lici.exercicio      = mapa.exercicio     \n";
    $stSql .= " AND     lici.cod_mapa       = mapa.cod_mapa     \n";
    $stSql .= " AND     mapa.exercicio      = mait.exercicio     \n";
    $stSql .= " AND     mapa.cod_mapa       = mait.cod_mapa     \n";
    $stSql .= " AND     mait.cod_item       = cait.cod_item     \n";
    $stSql .= " AND     cait.cod_grandeza   = unme.cod_grandeza     \n";
    $stSql .= " AND     cait.cod_unidade    = unme.cod_unidade     \n";
    $stSql .= " AND     lici.exercicio      = 2007     \n";
    $stSql .= " AND     lici.exercicio='".$this->getDado('exercicio')."'                      \n";
    if (trim($this->getDado('stEntidades'))) {
        $stSql .= " AND     lici.cod_entidade IN (".$this->getDado('stEntidades').")              \n";
    }
    $stSql .= " GROUP BY lici.exercicio     \n";
    $stSql .= "         ,lici.cod_licitacao     \n";
    $stSql .= "         ,mait.cod_item     \n";
    $stSql .= "         ,cait.cod_item     \n";
    $stSql .= "         ,cait.descricao     \n";
    $stSql .= "         ,to_char(lici.timestamp,'dd/mm/yyyy')     \n";
    $stSql .= "         ,to_char(lici.timestamp,'yyyymm')     \n";
    $stSql .= "         ,unme.simbolo     \n";
    $stSql .= " ORDER BY lici.exercicio     \n";
    $stSql .= "         ,lici.cod_licitacao     \n";
    $stSql .= "         ,mait.cod_item     \n";
    $stSql .= "         ,cait.cod_item     \n";
    $stSql .= "         ,cait.descricao     \n";
    $stSql .= "         ,to_char(lici.timestamp,'dd/mm/yyyy')     \n";
    $stSql .= "         ,to_char(lici.timestamp,'yyyymm')     \n";
    $stSql .= "         ,unme.simbolo     \n";

    return $stSql;
}

}
