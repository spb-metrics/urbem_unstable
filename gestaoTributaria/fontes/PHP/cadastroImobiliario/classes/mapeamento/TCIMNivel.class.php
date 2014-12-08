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
     * Classe de mapeamento para a tabela IMOBILIARIO.NIVEL
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMNivel.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.02
*/

/*
$Log$
Revision 1.7  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.NIVEL
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMNivel extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMNivel()
{
    parent::Persistente();
    $this->setTabela('imobiliario.nivel');

    $this->setCampoCod('cod_nivel');
    $this->setComplementoChave('cod_vigencia');

    $this->AddCampo('cod_nivel','integer',true,'',true,false);
    $this->AddCampo('cod_vigencia','integer',true,'',true,true);
    $this->AddCampo('nom_nivel','varchar',true,'80',false,false);
    $this->AddCampo('mascara','varchar',true,'80',false,false);

}

function recuperaRelacionamentoConsulta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoConsulta().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoConsulta()
{
    $stSQL  = " SELECT                                                      \n";
    $stSQL .= "    niv.cod_nivel,                                           \n";
    $stSQL .= "    niv.cod_vigencia,                                        \n";
    $stSQL .= "    niv.nom_nivel,                                           \n";
    $stSQL .= "    niv.mascara,                                             \n";
    $stSQL .= "    TO_CHAR(iv.dt_inicio, 'dd/mm/yyyy') AS dt_inicio         \n";
    $stSQL .= "FROM                                                         \n";
    $stSQL .= "    imobiliario.nivel AS niv                                 \n";
    $stSQL .= "LEFT JOIN                                                    \n";
    $stSQL .= "    imobiliario.vigencia AS iv                               \n";
    $stSQL .= "ON                                                           \n";
    $stSQL .= "    iv.cod_vigencia = niv.cod_vigencia                       \n";

    return $stSQL;
}

}
