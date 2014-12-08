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
    * Classe de mapeamento da tabela ARRECADACAO.PERMISSAO
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRPermissao.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.02
*/

/*
$Log$
Revision 1.9  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.8  2006/09/15 10:41:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.PERMISSAO
  * Data de Criação: 18/05/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRPermissao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRPermissao()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.permissao');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_grupo,numcgm');

    $this->AddCampo('cod_grupo','integer',true,'',true,true);
    $this->AddCampo('numcgm','integer',true,'',true,true);
    $this->AddCampo('ano_exercicio', 'varchar', true, '4', true, true );

}
function recuperaRelacionamento(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_grupo ";
    $stSql  = $this->montaRecuperaRelacionamento().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT                                          \r\n";
    $stSql .= "    ap.cod_grupo as cod_grupo,                  \r\n";
    $stSql .= "    ap.numcgm as numcgm,                        \r\n";
    $stSql .= "    agc.descricao as descricao,                 \r\n";
    $stSql .= "    agc.ano_exercicio as ano_exercicio,         \r\n";
    $stSql .= "    userx.username as username,                 \r\n";
    $stSql .= "    cgm.nom_cgm as nom_cgm                      \r\n";
    $stSql .= "FROM                                            \r\n";
    $stSql .= "    arrecadacao.permissao       as ap,          \r\n";
    $stSql .= "    arrecadacao.grupo_credito   as agc,         \r\n";
    $stSql .= "    administracao.usuario       as userx,       \r\n";
    $stSql .= "    sw_cgm                      as cgm          \r\n";
    $stSql .= "WHERE                                           \r\n";
    $stSql .= "    ap.ano_exercicio = agc.ano_exercicio AND    \r\n";
    $stSql .= "    ap.cod_grupo = agc.cod_grupo AND            \r\n";
    $stSql .= "    ap.numcgm    = userx.numcgm  AND            \r\n";
    $stSql .= "    userx.numcgm  = cgm.numcgm                  \r\n";

    return $stSql;

}

}// fecha classe
?>
