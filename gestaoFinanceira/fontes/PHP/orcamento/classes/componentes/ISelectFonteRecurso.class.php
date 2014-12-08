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
    * Arquivo de textbox e select entidade geral
    * Data de Criação: 22/06/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage

    $Revision: 30824 $
    $Name$
    $Author: jose.eduardo $
    $Date: 2006-08-17 15:47:41 -0300 (Qui, 17 Ago 2006) $

    * Casos de uso: uc-02.01.02
*/

/*
$Log$
Revision 1.1  2006/08/17 18:47:41  jose.eduardo
Bug #6739#

*/

include_once ( CLA_SELECT );

class ISelectFonteRecurso extends Select
{
function ISelectFonteRecurso()
{
    parent::Select();

    $this->setName      ( 'inCodFonteRecurso'     );
    $this->setValue     ( ''                      );
    $this->setRotulo    ( 'Grupo Fonte de Recurso');
    $this->setStyle     ( "width: 400px"          );
    $this->setNull      ( false                   );
    $this->setCampoId   ( "cod_fonte"             );
    $this->setCampoDesc ( "[cod_fonte] - [descricao]" );
    $this->addOption    ( "", "Selecione"         );

}

function montaHTML()
{
    $rsFonteRecurso = new RecordSet;
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoFonteRecurso.class.php"    );
    $obTOrcamentoFonteRecurso = new TOrcamentoFonteRecurso;
    $obTOrcamentoFonteRecurso->recuperaRelacionamento( $rsFonteRecurso, "" );
    $this->preencheCombo       ( $rsFonteRecurso );
    parent::montaHTML();
}
}
?>
