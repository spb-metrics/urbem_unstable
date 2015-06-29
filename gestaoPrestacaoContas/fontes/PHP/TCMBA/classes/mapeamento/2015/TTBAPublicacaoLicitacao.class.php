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

    $Revision: 62823 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

/*
$Log$
Revision 1.4  2007/10/03 02:50:44  diego
Corrigindo formatação

Revision 1.3  2007/10/02 18:20:03  hboaventura
inclusão do caso de uso uc-06.05.00

Revision 1.2  2007/10/01 04:41:09  diego
Correção na formatação de data

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
class TTBAPublicacaoLicitacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTBAPublicacaoLicitacao()
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
    $stSql .= " SELECT   edit.exercicio     \n";
    $stSql .= "         ,edit.cod_licitacao     \n";
    $stSql .= "         ,to_char(pued.data_publicacao,'dd/mm/yyyy') as data_publicacao     \n";
    $stSql .= "         ,cgm.nom_cgm as nome_veiculo     \n";
    $stSql .= "         ,substr(pued.oid,3,6)::integer as numero     \n";
    $stSql .= "         ,to_char(pued.data_publicacao,'yyyymm') as competencia     \n";
    $stSql .= " FROM     licitacao.edital               as edit     \n";
    $stSql .= "         ,licitacao.publicacao_edital    as pued     \n";
    $stSql .= "         ,licitacao.veiculos_publicidade as vepu     \n";
    $stSql .= "         ,sw_cgm                         as cgm     \n";
    $stSql .= " WHERE   edit.exercicio      = pued.exercicio     \n";
    $stSql .= " AND     edit.num_edital     = pued.num_edital     \n";
    $stSql .= " AND     pued.numcgm         = vepu.numcgm     \n";
    $stSql .= " AND     vepu.numcgm         = cgm.numcgm     \n";
    $stSql .= " AND     edit.exercicio='".$this->getDado('exercicio')."'                      \n";
    if (trim($this->getDado('stEntidades'))) {
        $stSql .= " AND     edit.cod_entidade IN (".$this->getDado('stEntidades').")              \n";
    }

    return $stSql;
}

}
